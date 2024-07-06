<?php

declare(strict_types=1);

namespace App\Handler\UserRegistration;

use App\ActorSystem\AppActor;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

readonly class CreateUserHandlerFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): CreateUserHandler
    {
        return new CreateUserHandler($container->get(AppActor::class));
    }
}
