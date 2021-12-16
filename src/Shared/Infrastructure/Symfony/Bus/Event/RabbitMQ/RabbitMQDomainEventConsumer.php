<?php

declare(strict_types=1);

namespace Shared\Infrastructure\Symfony\Bus\Event\RabbitMQ;

use AMQPEnvelope;
use AMQPQueue;
use AMQPQueueException;
use Shared\Infrastructure\Symfony\Bus\Event\DomainEventJsonDeserializer;

final class RabbitMQDomainEventConsumer
{
    public function __construct(
        private RabbitMQConnection $connection,
        private DomainEventJsonDeserializer $deserializer
    ) {}

    public function consume(callable $subscriber, string $queue_name): void
    {
        try {
            $this->connection->queue($queue_name)->consume(
                function (AMQPEnvelope $envelope, AMQPQueue $queue) use ($subscriber) {
                    $event = $this->deserializer->deserialize($envelope->getBody());
                    $subscriber($event);
                    $queue->ack($envelope->getDeliveryTag());

                    return false;
                }
            );
        } catch (AMQPQueueException $e) {}
    }
}
