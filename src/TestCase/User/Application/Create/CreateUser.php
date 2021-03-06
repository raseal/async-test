<?php

declare(strict_types=1);

namespace TestCase\User\Application\Create;

use Shared\Domain\Bus\Event\EventBus;
use TestCase\User\Domain\Exception\UserAlreadyExists;
use TestCase\User\Domain\User;
use TestCase\User\Domain\UserEmail;
use TestCase\User\Domain\UserId;
use TestCase\User\Domain\UserRepository;

final class CreateUser
{
    public function __construct(
        private UserRepository $user_repository,
        private EventBus $event_bus
    ) {}

    public function __invoke(UserId $user_id, UserEmail $user_email): void
    {
        $user = $this->user_repository->findById($user_id);

        if (null !== $user) {
            throw new UserAlreadyExists($user_id);
        }

        $user = User::create($user_id, $user_email);
        $this->user_repository->save($user);
        $this->event_bus->publish(...$user->pullDomainEvents());
    }
}
