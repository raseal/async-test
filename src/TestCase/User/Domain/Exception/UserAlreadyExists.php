<?php

declare(strict_types=1);

namespace TestCase\User\Domain\Exception;

use Shared\Domain\Exception\DomainError;
use TestCase\User\Domain\UserId;
use function sprintf;

final class UserAlreadyExists extends DomainError
{
    public function __construct(
        private UserId $user_id
    ) {
        parent::__construct();
    }

    public function errorCode(): string
    {
        return 'user_already_exists';
    }

    public function errorMessage(): string
    {
        return sprintf(
            'User [%s] already exists',
            $this->user_id->value()
        );
    }
}
