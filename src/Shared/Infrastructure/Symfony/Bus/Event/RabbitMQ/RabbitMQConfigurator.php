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
    public function __construct(
        private RabbitMQConnection $connection
    ) {}

    public function configure(string $exchange_name, DomainEventSubscriber ...$subscribers): void
    {
        // TODO: create retries & deadletters
        $this->declareExchange($exchange_name);
        $this->declareQueues($exchange_name, ...$subscribers);
    }

    private function declareExchange(string $exchange_name): void
    {
        $exchange = $this->connection->exchange($exchange_name);
        $exchange->setType(AMQP_EX_TYPE_TOPIC);
        $exchange->setFlags(AMQP_DURABLE);
        $exchange->declareExchange();
    }

    private function declareQueues(string $exchange_name, DomainEventSubscriber ...$subscribers): void
    {
        array_walk($subscribers, $this->queueDeclarer($exchange_name));
    }

    private function queueDeclarer(string $exchange_name): callable
    {
        return function (DomainEventSubscriber $subscriber) use ($exchange_name) {
            $queue_name = RabbitMQQueueNameFormatter::format($subscriber);
            $queue = $this->declareQueue($queue_name);
            $queue->bind($exchange_name, $queue_name);

            /** @var DomainEvent $event_class */
            foreach($subscriber::subscribedTo() as $event_class) {
                $queue->bind($exchange_name, $event_class::eventName());
            }
        };
    }

    private function declareQueue(string $name, int $messageTTL = null): AMQPQueue
    {
        $queue = $this->connection->queue($name);

        if (null !== $messageTTL) {
            $queue->setArgument('x-message-ttl', $messageTTL);
        }

        $queue->setFlags(AMQP_DURABLE);
        $queue->declareQueue();

        return $queue;
    }
}
