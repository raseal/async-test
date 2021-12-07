<?php

declare(strict_types=1);

namespace TestCase\User\Domain\Exception;

use Shared\Domain\Exception\DomainError;
use TestCase\User\Domain\UserId;
use function sprintf;

final class UserDoesNotExist extends DomainError
{
    public function __construct(
        private UserId $user_id
    ) {
        parent::__construct();
    }

    public function errorCode(): string
    {
        return 'user_does_not_exist';
    }

    public function errorMessage(): string
    {
        return sprintf(
            'User [%s] does not exist',
            $this->user_id->value()
        );
    }
}
