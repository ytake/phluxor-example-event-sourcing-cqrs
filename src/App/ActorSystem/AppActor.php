<?php

declare(strict_types=1);

namespace App\ActorSystem;

use Phluxor\ActorSystem;

readonly class AppActor
{
    public const string NAME = 'rest_api';

    public function __construct(
        public ActorSystem $system,
        public ActorSystem\Ref $actorRef
    ) {
    }
}
