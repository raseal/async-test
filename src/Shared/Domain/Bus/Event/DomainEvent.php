<?php

declare(strict_types=1);

namespace Shared\Domain\Bus\Event;

use DateTimeImmutable;
use Shared\Domain\ValueObject\Uuid;
use const DATE_ATOM;

abstract class DomainEvent
{
    private string $aggregate_id;
    private string $event_id;
    private string $occurred_on;

    public function __construct(
        string $aggregate_id,
        string $event_id = null,
        string $occurred_on = null
    ) {
        $this->aggregate_id = $aggregate_id;
        $this->event_id = $event_id ?? Uuid::random()->value();
        $this->occurred_on = $occurred_on ?? (new DateTimeImmutable())->format(DATE_ATOM);
    }

    abstract public function eventName(): string;

    public function aggregateId(): string
    {
        return $this->aggregate_id;
    }

    public function eventId(): string
    {
        return $this->event_id;
    }

    public function occurredOn(): string
    {
        return $this->occurred_on;
    }
}
