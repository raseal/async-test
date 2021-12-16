<?php

declare(strict_types=1);

namespace Shared\Infrastructure\Symfony\Bus\Event;

use RuntimeException;
use Shared\Domain\Bus\Event\DomainEvent;
use Shared\Domain\Utils;
use function sprintf;

final class DomainEventJsonDeserializer
{
    public function __construct(
        private DomainEventMapping $domain_event_mapping
    ) {}

    public function deserialize(string $domain_event): DomainEvent
    {
        $event_info = Utils::jsonDecode($domain_event);
        $event_data = $event_info['data'];
        $event_name = $event_data['type'];
        /** @var DomainEvent $event_class */
        $event_class = $this->domain_event_mapping->for($event_name);

        if (null === $event_class) {
            throw new RuntimeException(sprintf('event <%s> does not exist or has no subscribers', $event_name));
        }

        return $event_class::fromPrimitives(
            $event_data['attributes']['id'],
            $event_data['attributes'],
            $event_data['id'],
            $event_data['occurred_on']
        );
    }
}
