<?php

declare(strict_types=1);

namespace TestCase\User\Application\Notify;

use Exception;
use Shared\Domain\Bus\Event\DomainEventSubscriber;
use TestCase\User\Domain\UserEmailChanged;
use function json_encode;
use function sprintf;

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
        throw new Exception("catacras");
        echo sprintf(
            "You just arrived to the subscriber <%s> with the event %s \n",
            self::class,
            json_encode($event->toPrimitives())
        );
    }
}
