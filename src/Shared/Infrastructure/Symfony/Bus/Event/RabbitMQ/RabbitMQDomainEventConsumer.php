<?php

declare(strict_types=1);

namespace Shared\Infrastructure\Symfony\Bus\Event\RabbitMQ;

use AMQPEnvelope;
use AMQPQueue;
use AMQPQueueException;
use Exception;
use Shared\Infrastructure\Symfony\Bus\Event\DomainEventJsonDeserializer;
use Throwable;
use function Lambdish\Phunctional\assoc;
use function Lambdish\Phunctional\get;

final class RabbitMQDomainEventConsumer
{
    public function __construct(
        private RabbitMQConnection $connection,
        private DomainEventJsonDeserializer $deserializer,
        private string $exchange_name,
        private int $max_retries
    ) {}

    public function consume(callable $subscriber, string $queue_name): void
    {
        try {
            $this->connection->queue($queue_name)->consume($this->consumer($subscriber));
        } catch (AMQPQueueException $e) {}
    }

    private function consumer(callable $subscriber): callable
    {
        return function (AMQPEnvelope $envelope, AMQPQueue $queue) use ($subscriber) {
            $event = $this->deserializer->deserialize($envelope->getBody());

            try {
                $subscriber($event);
            } catch (Throwable $error) {
                $this->handleConsumptionError($envelope, $queue);
                throw $error;
            }

            $queue->ack($envelope->getDeliveryTag());

            return false;
        };
    }

    private function handleConsumptionError(AMQPEnvelope $envelope, AMQPQueue $queue): void
    {
        $this->hasBeenRedeliveredTooMuch($envelope)
            ? $this->sendToDeadLetter($envelope, $queue)
            : $this->sendToRetry($envelope, $queue);

        $queue->ack($envelope->getDeliveryTag());
    }

    private function hasBeenRedeliveredTooMuch(AMQPEnvelope $envelope): bool
    {
        return $this->redeliveryCount($envelope) >= $this->max_retries;
    }

    private function redeliveryCount(AMQPEnvelope $envelope): int
    {
        return $envelope->getHeaders()['redelivery_count'] ?? 0;
    }

    private function sendToDeadLetter(AMQPEnvelope $envelope, AMQPQueue $queue): void
    {
        $this->sendMessageTo(RabbitMQExchangeNameFormatter::deadLetter($this->exchange_name), $envelope, $queue);
    }

    private function sendToRetry(AMQPEnvelope $envelope, AMQPQueue $queue): void
    {
        $this->sendMessageTo(RabbitMQExchangeNameFormatter::retry($this->exchange_name), $envelope, $queue);
    }

    private function sendMessageTo(string $exchange_name, AMQPEnvelope $envelope, AMQPQueue $queue): void
    {
        $headers = $envelope->getHeaders();

        $this->connection->exchange($exchange_name)->publish(
            $envelope->getBody(),
            $queue->getName(),
            AMQP_NOPARAM,
            [
                'message_id' => $envelope->getMessageId(),
                'content_type' => $envelope->getContentType(),
                'content_encoding' => $envelope->getContentEncoding(),
                'priority' => $envelope->getPriority(),
                'headers' => assoc($headers, 'redelivery_count', get('redelivery_count', $headers, 0) + 1),
                //'headers' => $headers['redelivery_count'] = ($this->redeliveryCount($envelope) + 1),
            ]
        );
    }
}
