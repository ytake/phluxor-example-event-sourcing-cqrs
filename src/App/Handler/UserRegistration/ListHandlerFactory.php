<?php

declare(strict_types=1);

namespace App\Handler\UserRegistration;

use App\Query\RegistrationUser;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class ListHandlerFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): ListHandler
    {
        return new ListHandler($container->get(RegistrationUser::class));
    }
}
