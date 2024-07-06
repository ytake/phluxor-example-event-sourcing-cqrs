<?php

declare(strict_types=1);

namespace App\Message;

readonly class UserCreateError implements UserCreateMessageInterface
{
    public function __construct(
        public string $message
    ) {
    }

    public function isSuccess(): bool
    {
        return false;
    }
}
