<?php

declare(strict_types=1);

namespace TestCase\User\Application\Create;

use Shared\Domain\Bus\Command\Command;

final class CreateUserCommand implements Command
{
    public function __construct(
        private string $id,
        private string $email
    ) {}

    public function id(): string
    {
        return $this->id;
    }

    public function email(): string
    {
        return $this->email;
    }
}
