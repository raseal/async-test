<?php

declare(strict_types=1);

namespace TestCase\User\Domain;

final class User
{
    public function __construct(
        private UserId $user_id,
        private UserEmail $user_email
    ) {}

    public function userId(): UserId
    {
        return $this->user_id;
    }

    public function userEmail(): UserEmail
    {
        return $this->user_email;
    }
}
