<?php

declare(strict_types=1);

namespace TestCase\User\Domain;

use Shared\Domain\Bus\Event\DomainEvent;

final class UserAdded extends DomainEvent
{
    public function __construct(
        private string $user_id,
        private string $user_email,
        private ?string $event_id = null,
        private ?string $occurred_on = null
    ) {
        parent::__construct($user_id, $event_id, $occurred_on);
    }

    public static function create(User $user):self
    {
        return new self($user->userId()->value(), $user->userEmail()->value());
    }

    public static function eventName(): string
    {
        return 'user.added';
    }

    static public function fromPrimitives(
        string $aggregate_id,
        array $body,
        string $event_id,
        string $occurred_on
    ): DomainEvent {
        return new self(
            $aggregate_id,
            $body['user_email'],
            $event_id,
            $occurred_on
        );
    }

    public function toPrimitives(): array
    {
        return [
            'id' => $this->user_id,
            'user_email' => $this->user_email,
        ];
    }

    public function userEmail(): string
    {
        return $this->user_email;
    }
}
