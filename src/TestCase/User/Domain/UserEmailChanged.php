<?php

declare(strict_types=1);

namespace TestCase\User\Domain;

use Shared\Domain\Bus\Event\DomainEvent;

final class UserEmailChanged extends DomainEvent
{
    private string $user_email;

    public function __construct(
        string $user_id,
        string $user_email,
        string $event_id = null,
        string $occurred_on = null
    ) {
        parent::__construct($user_id, $event_id, $occurred_on);
        $this->user_email = $user_email;
    }

    public static function create(User $user):self
    {
        return new self($user->userId()->value(), $user->userEmail()->value());
    }

    public static function eventName(): string
    {
        return 'user.email.changed';
    }

    public function userEmail(): string
    {
        return $this->user_email;
    }
}
