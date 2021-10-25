<?php

declare(strict_types=1);

namespace Shared\Infrastructure\Symfony\Bus\Event;

use Shared\Domain\Bus\Event\DomainEvent;
use Shared\Domain\Bus\Event\EventBus;
use Symfony\Component\Messenger\MessageBusInterface;
use Throwable;

class SymfonySyncEventBus implements EventBus
{
    public function __construct(
        private MessageBusInterface $bus
    ) {}

    public function publish(DomainEvent ...$events): void
    {
        foreach ($events as $event) {
            try {
                $this->bus->dispatch($event);
            } catch (Throwable) {
            }
        }
    }
}
