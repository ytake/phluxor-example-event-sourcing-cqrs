<?php

declare(strict_types=1);

namespace App\ActorSystem\UserRegistration;

use App\Database\RegisteredUser;
use App\Event\ProtoBuf\UserCreated;
use Phluxor\ActorSystem\Context\ContextInterface;
use Phluxor\ActorSystem\Message\ActorInterface;

readonly class ReadModelUpdateActor implements ActorInterface
{
    public function __construct(
        private RegisteredUser $registeredUser
    ) {
    }

    public function receive(ContextInterface $context): void
    {
        $msg = $context->message();
        if ($msg instanceof UserCreated) {
            $row = $this->registeredUser->findByEmail($msg->getEmail());
            if (!$row) {
                $result = $this->registeredUser->addUser($msg->getUserID(), $msg->getUserName(), $msg->getEmail());
                if (!$result) {
                    $context->logger()->error(
                        'failed to add user',
                        [
                            'user_id' => $msg->getUserID(),
                            'user_name' => $msg->getUserName(),
                            'email' => $msg->getEmail()
                        ]
                    );
                }
            }
        }
        // no send message
        // read model update actor is a final actor
    }
}
