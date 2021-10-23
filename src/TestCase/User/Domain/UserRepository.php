<?php

declare(strict_types=1);

namespace TestCase\User\Domain;

interface UserRepository
{
    public function findById(UserId $user_id): ?User;

    public function save(User $user): void;
}
