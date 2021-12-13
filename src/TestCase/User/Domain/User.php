<?php

declare(strict_types=1);

namespace TestCase\User\Domain;

use Shared\Domain\Aggregate\AggregateRoot;

final class User extends AggregateRoot
{
    public function __construct(
        private UserId $user_id,
        private UserEmail $user_email
    ) {}

    public static function create(UserId $user_id, UserEmail $user_email): self
    {
        $user = new self($user_id, $user_email);

        $user->record(UserAdded::create($user));

        return $user;
    }

    public function userId(): UserId
    {
        return $this->user_id;
    }

    public function userEmail(): UserEmail
    {
        return $this->user_email;
    }

    public function changeEmail(UserEmail $user_email): void
    {
        $this->user_email = $user_email;
        $this->record(UserEmailChanged::create($this));
    }
}
