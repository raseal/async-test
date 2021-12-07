<?php

declare(strict_types=1);

namespace TestCase\User\Application\Change;

use Shared\Domain\Bus\Event\EventBus;
use TestCase\User\Domain\Exception\UserDoesNotExist;
use TestCase\User\Domain\UserEmail;
use TestCase\User\Domain\UserId;
use TestCase\User\Domain\UserRepository;

final class ChangeUserEmail
{
    public function __construct(
        private UserRepository $user_repository,
        private EventBus $event_bus
    ) {}

    public function __invoke(UserId $user_id, UserEmail $user_email): void
    {
        $user = $this->user_repository->findById($user_id);

        if (null === $user) {
            throw new UserDoesNotExist($user_id);
        }

        $user->changeEmail($user_email);
        $this->user_repository->save($user);
        $this->event_bus->publish(...$user->pullDomainEvents());
    }
}
