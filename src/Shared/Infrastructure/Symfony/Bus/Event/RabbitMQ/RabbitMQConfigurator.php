<?php

declare(strict_types=1);

namespace Shared\Infrastructure\Symfony\Bus\Event\RabbitMQ;

use AMQPQueue;
use Shared\Domain\Bus\Event\DomainEvent;
use Shared\Domain\Bus\Event\DomainEventSubscriber;
use function array_walk;
use const AMQP_DURABLE;
use const AMQP_EX_TYPE_TOPIC;

final class RabbitMQConfigurator
{
    private const TTL = 1000;

    public function __construct(
        private RabbitMQConnection $connection
    ) {}

    public function configure(string $exchange_name, DomainEventSubscriber ...$subscribers): void
    {
        $retry_exchange_name = RabbitMqExchangeNameFormatter::retry($exchange_name);
        $deadLetter_exchange_name = RabbitMqExchangeNameFormatter::deadLetter($exchange_name);

        $this->declareExchange($exchange_name);
        $this->declareExchange($retry_exchange_name);
        $this->declareExchange($deadLetter_exchange_name);

        $this->declareQueues($exchange_name, $retry_exchange_name, $deadLetter_exchange_name, ...$subscribers);
    }

    private function declareExchange(string $exchange_name): void
    {
        $exchange = $this->connection->exchange($exchange_name);
        $exchange->setType(AMQP_EX_TYPE_TOPIC);
        $exchange->setFlags(AMQP_DURABLE);
        $exchange->declareExchange();
    }

    private function declareQueues(
        string $exchange_name,
        string $retry_exchange_name,
        string $deadLetter_exchange_name,
        DomainEventSubscriber ...$subscribers
    ): void
    {
        array_walk($subscribers, $this->queueDeclarer($exchange_name, $retry_exchange_name, $deadLetter_exchange_name));
    }

    private function queueDeclarer(
        string $exchange_name,
        string $retry_exchange_name,
        string $deadLetter_exchange_name
    ): callable
    {
        return function (DomainEventSubscriber $subscriber) use (
            $exchange_name,
            $retry_exchange_name,
            $deadLetter_exchange_name
        ) {
            $queue_name = RabbitMQQueueNameFormatter::format($subscriber);
            $retry_queue_name = RabbitMqQueueNameFormatter::formatRetry($subscriber);
            $deadLetter_queue_name = RabbitMqQueueNameFormatter::formatDeadLetter($subscriber);

            $queue = $this->declareQueue($queue_name);
            $retry_queue = $this->declareQueue($retry_queue_name, $exchange_name, $queue_name, self::TTL);
            $deadLetter_queue = $this->declareQueue($deadLetter_queue_name);

            $queue->bind($exchange_name, $queue_name);
            $retry_queue->bind($retry_exchange_name, $queue_name);
            $deadLetter_queue->bind($deadLetter_exchange_name, $queue_name);

            /** @var DomainEvent $event_class */
            foreach($subscriber::subscribedTo() as $event_class) {
                $queue->bind($exchange_name, $event_class::eventName());
            }
        };
    }

    private function declareQueue(
        string $name,
        string $deadLetter_exchange = null,
        string $deadLetter_routing_key = null,
        int $messageTTL = null
    ): AMQPQueue
    {
        $queue = $this->connection->queue($name);

        if (null !== $deadLetter_exchange) {
            $queue->setArgument('x-dead-letter-exchange', $deadLetter_exchange);
        }

        if (null !== $deadLetter_routing_key) {
            $queue->setArgument('x-dead-letter-routing-key', $deadLetter_routing_key);
        }

        if (null !== $messageTTL) {
            $queue->setArgument('x-message-ttl', $messageTTL);
        }

        $queue->setFlags(AMQP_DURABLE);
        $queue->declareQueue();

        return $queue;
    }
}
