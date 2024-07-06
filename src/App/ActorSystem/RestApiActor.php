<?php

declare(strict_types=1);

namespace App\ActorSystem;

use App\ActorSystem\UserRegistration\ReadModelUpdateActor;
use App\ActorSystem\UserRegistration\UserActor;
use App\Command\CreateUser;
use App\Database\RegisteredUser;
use App\Message\UserCreateError;
use Closure;
use Phluxor\ActorSystem\Context\ContextInterface;
use Phluxor\ActorSystem\Exception\NameExistsException;
use Phluxor\ActorSystem\Message\ActorInterface;
use Phluxor\ActorSystem\Message\Started;
use Phluxor\ActorSystem\Props;
use Phluxor\ActorSystem\Ref;
use Phluxor\ActorSystem\SpawnResult;
use Phluxor\Persistence\EventSourcedReceiver;
use Phluxor\Persistence\ProviderInterface;
use Phluxor\Persistence\ProviderStateInterface;
use Swoole\Database\PDOProxy;

class RestApiActor implements ActorInterface
{
    private ?Ref $readModelUpdater = null;

    /**
     * @param Closure(int): ProviderStateInterface&ProviderInterface $providerState
     */
    public function __construct(
        private readonly PDOProxy $proxy,
        private readonly Closure $providerState
    ) {
    }

    public function receive(ContextInterface $context): void
    {
        $msg = $context->message();
        switch (true) {
            case $msg instanceof Started:
                $this->readModelUpdater = $context->spawn(
                    Props::fromProducer(
                        fn() => new ReadModelUpdateActor(new RegisteredUser($this->proxy))
                    )
                );
                break;
            case $msg instanceof CreateUser:
                $provider = $this->providerState;
                $result = $this->spawnUserActor($context, $this->readModelUpdater, $provider, $msg);
                if ($result->isError() instanceof NameExistsException) {
                    $context->send(
                        $msg->ref,
                        new UserCreateError(
                            sprintf("user %s already exists", $msg->userName)
                        )
                    );
                    return;
                }
                $context->send($result->getRef(), $msg);
                break;
        }
    }

    /**
     * @param ContextInterface $context
     * @param Ref $readModelUpdater
     * @param Closure(int): ProviderStateInterface&ProviderInterface $provider
     * @param mixed $msg
     * @return SpawnResult
     */
    public function spawnUserActor(
        ContextInterface $context,
        Ref $readModelUpdater,
        Closure $provider,
        mixed $msg
    ): SpawnResult {
        $stateProvider = $provider(3);
        return $context->spawnNamed(
            Props::fromProducer(fn() => new UserActor($readModelUpdater),
                Props::withReceiverMiddleware(
                    new EventSourcedReceiver(
                        $stateProvider
                    )
                )
            ),
            sprintf("user:%s", $msg->email)
        );
    }
}
