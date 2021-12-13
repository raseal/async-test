<?php

declare(strict_types=1);

namespace TestCase\User\Application\Notify;

use Shared\Domain\Bus\Event\DomainEventSubscriber;
use TestCase\User\Domain\UserEmailChanged;

final class NotifyOnEmailChanged implements DomainEventSubscriber
{
    public static function subscribedTo(): array
    {
        return [
            UserEmailChanged::class,
        ];
    }

    public function __invoke(UserEmailChanged $event): void
    {
        //dummy method to simulate the notification
    }
}
