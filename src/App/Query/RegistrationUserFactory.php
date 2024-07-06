<?php

namespace App\Query;

use Swoole\Database\PDOProxy;
use Psr\Container\ContainerInterface;

class RegistrationUserFactory
{
    public function __invoke(ContainerInterface $container): RegistrationUser
    {
        return new RegistrationUser($container->get(PDOProxy::class));
    }
}
