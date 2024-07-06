<?php

declare(strict_types=1);

namespace App\ActorSystem\UserRegistration;

use App\Command\CreateUser;
use App\Event\ProtoBuf\UserCreated;
use App\Message\UserCreateError;
use App\Message\UserCreateResponse;
use Google\Protobuf\Internal\Message;
use Phluxor\ActorSystem\Context\ContextInterface;
use Phluxor\ActorSystem\Message\ActorInterface;
use Phluxor\ActorSystem\Ref;
use Phluxor\Persistence\Message\ReplayComplete;
use Phluxor\Persistence\Message\RequestSnapshot;
use Phluxor\Persistence\PersistentInterface;
use Phluxor\Persistence\Mixin;
use Symfony\Component\Uid\Ulid;

class UserActor implements ActorInterface, PersistentInterface
{
    use Mixin;

    private ?UserCreated $state = null;
    private $version = 0;

    public function __construct(
        private readonly Ref $readModelUpdater
    ) {
    }

    public function receive(ContextInterface $context): void
    {
        $msg = $context->message();
        switch (true) {
            case $msg instanceof RequestSnapshot:
                // save the state
                if ($this->state != null) {
                    $this->persistenceSnapshot($this->state);
                }
                break;
            case $msg instanceof ReplayComplete:
                if ($this->state != null) {
                    $context->logger()->info(
                        sprintf('Replay complete for %s', $this->state->serializeToJsonString())
                    );
                }
                break;
            case $msg instanceof CreateUser:
                if ($this->isStateExists($msg->email)) {
                    $context->send($msg->ref, new UserCreateError('user already exists'));
                    return;
                }
                // サンプルでは省いていますがversion を利用して楽観的ロックを実現することができます
                $id = Ulid::generate();
                $ev = new UserCreated([
                    'userID' => $id,
                    'email' => $msg->email,
                    'userName' => $msg->userName,
                    'version' => $this->version++,
                ]);
                $this->persist($ev);
                $context->send($msg->ref, new UserCreateResponse($id));
                $context->send($this->readModelUpdater, $ev);
                break;
            case $msg instanceof Message:
                // event がリプレイされた場合は状態を更新する
                if ($msg->serializeToJsonString() != '') {
                    if ($msg instanceof UserCreated) {
                        // 状態を復元します
                        $this->state = $msg;
                    }
                }
                break;
            default:
        }
    }

    private function isStateExists(string $email): bool
    {
        if ($this->state === null) {
            return false;
        }
        return $this->state->getEmail() == $email;
    }

    private function persist(Message $msg): void
    {
        if (!$this->recovering()) {
            $this->persistenceReceive($msg);
        }
        if ($msg instanceof UserCreated) {
            $this->state = $msg;
        }
    }
}
