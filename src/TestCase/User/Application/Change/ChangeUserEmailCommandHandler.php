<?php

declare(strict_types=1);

namespace TestCase\User\Application\Change;

use Shared\Domain\Bus\Command\CommandHandler;
use TestCase\User\Domain\UserEmail;
use TestCase\User\Domain\UserId;

final class ChangeUserEmailCommandHandler implements CommandHandler
{
    public function __construct(
        private ChangeUserEmail $create_user
    ) {}

    public function __invoke(ChangeUserEmailCommand $command): void
    {
        $this->create_user->__invoke(
            new UserId($command->id()),
            new UserEmail($command->email())
        );
    }
}
