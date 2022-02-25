<?php

declare(strict_types=1);

namespace Shared\Infrastructure\Symfony\Bus\Event\RabbitMQ;

use Shared\Domain\Bus\Event\DomainEventSubscriber;
use function array_map;
use function end;

final class RabbitMQQueueNameFormatter
{
    public static function format(DomainEventSubscriber $subscriber): string
    {
        $subscriber_classpath = explode('\\', get_class($subscriber));
        $queue_name_parts = [
            $subscriber_classpath[0],
            end($subscriber_classpath),
        ];

        return implode('.', array_map(fn(string $text) => self::toSnakeCase($text), $queue_name_parts));
    }

    public static function formatRetry(DomainEventSubscriber $subscriber): string
    {
        $queueName = self::format($subscriber);

        return "retry.$queueName";
    }

    public static function formatDeadLetter(DomainEventSubscriber $subscriber): string
    {
        $queueName = self::format($subscriber);

        return "dead_letter.$queueName";
    }

    public static function toSnakeCase(string $text): string
    {
        return ctype_lower($text) ? $text : strtolower(preg_replace('`([^A-Z\s])([A-Z])`', '$1_$2', $text));
    }
}
