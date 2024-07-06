<?php

declare(strict_types=1);

namespace App\Message;

interface UserCreateMessageInterface
{
    public function isSuccess(): bool;
}
