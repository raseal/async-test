<?php

declare(strict_types=1);

namespace Shared\Infrastructure\Symfony\Bus\Event\Sync;

use Shared\Domain\Bus\Event\DomainEvent;
use Shared\Domain\Bus\Event\EventBus;
use Symfony\Component\Messenger\Exception\NoHandlerForMessageException;
use Symfony\Component\Messenger\MessageBusInterface;

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
            } catch (NoHandlerForMessageException) {
            }
        }
    }
}
