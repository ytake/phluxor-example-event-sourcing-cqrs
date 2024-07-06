<?php

declare(strict_types=1);

namespace App\Message;

readonly class UserCreateResponse implements UserCreateMessageInterface
{
    public function __construct(
        public string $userID
    ) {
    }

    public function isSuccess(): bool
    {
        return true;
    }
}
