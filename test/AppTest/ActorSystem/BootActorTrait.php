<?php

declare(strict_types=1);

namespace AppTest\ActorSystem;

use App\ActorSystem\AppActor;
use App\ActorSystem\RestApiActor;
use App\Database\MysqlConnection;
use Closure;
use Phluxor\ActorSystem;
use Phluxor\ActorSystem\Props;
use Phluxor\Persistence\InMemoryProvider;
use Phluxor\Persistence\ProviderInterface;
use Phluxor\Persistence\ProviderStateInterface;

trait BootActorTrait
{
    private function bootActor(
        ActorSystem $system
    ): ActorSystem\SpawnResult {
        return $system->root()->spawnNamed(
            Props::fromProducer(
                fn() => new RestApiActor(
                    (new MysqlConnection())->proxy(),
                    $this->inMemoryState(new InMemoryProvider(3)),
                )
            ),
            AppActor::NAME
        );
    }

    /**
     * @return Closure(int): ProviderStateInterface&ProviderInterface $providerState
     */
    private function inMemoryState(InMemoryProvider $provider): Closure
    {
        return function (int $snapshotInterval) use ($provider) {
            return new readonly class($provider) implements ProviderInterface {
                /**
                 * @param InMemoryProvider $provider
                 */
                public function __construct(
                    private InMemoryProvider $provider,
                ) {
                }

                public function getState(): ProviderStateInterface
                {
                    return $this->provider;
                }
            };
        };
    }
}