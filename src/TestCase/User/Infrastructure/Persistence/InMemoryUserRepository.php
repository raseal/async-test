<?php

declare(strict_types=1);

namespace TestCase\User\Infrastructure\Persistence;

use TestCase\User\Domain\User;
use TestCase\User\Domain\UserId;
use TestCase\User\Domain\UserRepository;
use TestCase\User\Infrastructure\Persistence\Data\UserData;

final class InMemoryUserRepository implements UserRepository
{
    private array $users;

    public function __construct()
    {
        $this->users = UserData::users();
    }

    public function findById(UserId $user_id): ?User
    {
        return $this->users[$user_id->value()] ?? null;
    }

    public function save(User $user): void
    {
        // simulate user creation
    }
}
