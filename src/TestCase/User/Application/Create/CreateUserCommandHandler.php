<?php

declare(strict_types=1);

namespace TestCase\User\Application\Create;

use Shared\Domain\Bus\Command\CommandHandler;
use TestCase\User\Domain\UserEmail;
use TestCase\User\Domain\UserId;

final class CreateUserCommandHandler implements CommandHandler
{
    public function __construct(
        private CreateUser $create_user
    ) {}

    public function __invoke(CreateUserCommand $command): void
    {
        $this->create_user->__invoke(
            new UserId($command->id()),
            new UserEmail($command->email())
        );
    }
}
