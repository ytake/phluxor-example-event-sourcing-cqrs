<?php

declare(strict_types=1);

namespace App\ActorSystem;

use App\Database\MysqlConnection;
use Closure;
use Laminas\ServiceManager\ServiceManager;
use Mezzio\Swoole\Event\WorkerStartEvent;
use Phluxor\ActorSystem;
use Phluxor\ActorSystem\Props;
use Phluxor\Persistence\Mysql\DefaultSchema;
use Phluxor\Persistence\Mysql\MysqlProvider;
use Phluxor\Persistence\ProviderInterface;
use Phluxor\Persistence\ProviderStateInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Swoole\Database\PDOProxy;

readonly class BootAppActor
{
    public function __construct(
        private ContainerInterface $container
    ) {
    }

    public function __invoke(WorkerStartEvent $event): void
    {
        $system = ActorSystem::create();
        $proxy = $this->container->get(PDOProxy::class);
        $spawned = $system->root()->spawnNamed(
            Props::fromProducer(
                fn() => new RestApiActor(
                    $proxy,
                    $this->stateProvider($proxy, $system->getLogger()),
                )
            ),
            AppActor::NAME
        );
        if ($this->container instanceof ServiceManager) {
            $this->container->setService(
                AppActor::class,
                new AppActor($system, $spawned->getRef())
            );
            return;
        }
        throw new RuntimeException('Container is not a ServiceManager');
    }

    /**
     * @param PDOProxy $proxy
     * @param LoggerInterface $logger
     * @return Closure(int): ProviderStateInterface&ProviderInterface
     */
    private function stateProvider(
        PDOProxy $proxy,
        LoggerInterface $logger
    ): Closure {
        return function (int $snapshotInterval) use ($proxy, $logger) {
            return new MysqlProvider(
                $proxy,
                new DefaultSchema(),
                $snapshotInterval,
                $logger
            );
        };
    }
}
