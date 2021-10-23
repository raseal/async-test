<?php

declare(strict_types=1);

namespace TestCase\User\Infrastructure\Persistence\Data;

use TestCase\User\Domain\User;
use TestCase\User\Domain\UserEmail;
use TestCase\User\Domain\UserId;

final class UserData
{
    public static function users(): array
    {
        return [
            '7e5070c0-e287-4cab-b267-0d399665aac6' => new User(
                new UserId('7e5070c0-e287-4cab-b267-0d399665aac6'),
                new UserEmail('user1@mail.com')
            ),
            '85e5e2cb-cb4e-4a51-861e-8919c9ddc515' => new User(
                new UserId('85e5e2cb-cb4e-4a51-861e-8919c9ddc515'),
                new UserEmail('user2@another.mail.com')
            ),
            'ba213198-5ec3-45e0-be6f-93e0ca007907' => new User(
                new UserId('ba213198-5ec3-45e0-be6f-93e0ca007907'),
                new UserEmail('user3@last-email.com')
            )
        ];
    }
}
