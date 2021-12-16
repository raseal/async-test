<?php

declare(strict_types=1);

namespace Shared\Infrastructure\Symfony\Bus\Event;

use Shared\Domain\Bus\Event\DomainEvent;
use function json_encode;

final class DomainEventJsonSerializer
{
    public static function serialize(DomainEvent $domain_event): string
    {
        return json_encode(
            [
                'data' => [
                    'id'          => $domain_event->eventId(),
                    'type'        => $domain_event::eventName(),
                    'occurred_on' => $domain_event->occurredOn(),
                    'attributes'  => array_merge($domain_event->toPrimitives(), ['id' => $domain_event->aggregateId()]),
                ],
            ]
        );
    }
}
