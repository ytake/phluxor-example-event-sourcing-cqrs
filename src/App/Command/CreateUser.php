<?php

declare(strict_types=1);

namespace App\Command;

use Phluxor\ActorSystem\Ref;

readonly class CreateUser
{
    public function __construct(
        public string $email,
        public string $userName,
        public Ref $ref
    ) {
    }
}
