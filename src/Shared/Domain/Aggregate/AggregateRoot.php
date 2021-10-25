<?php

declare(strict_types=1);

namespace Shared\Domain\Aggregate;

use Shared\Domain\Bus\Event\DomainEvent;

abstract class AggregateRoot
{
    private $domain_events = [];

    final protected function record(DomainEvent $domain_event): void
    {
        $this->domain_events[] = $domain_event;
    }

    final public function pullDomainEvents(): array
    {
        $domain_events = $this->domain_events;
        $this->domain_events = [];

        return $domain_events;
    }
}
