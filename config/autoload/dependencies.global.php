<?php

declare(strict_types=1);

use App\ActorSystem\BootAppActor;
use App\ActorSystem\BootAppActorFactory;
use App\Database\MysqlConnectionFactory;

return [
    // Provides application-wide services.
    // We recommend using fully-qualified class names whenever possible as
    // service names.
    'dependencies' => [
        // Use 'aliases' to alias a service name to another service. The
        // key is the alias name, the value is the service to which it points.
        'aliases' => [
            // Fully\Qualified\ClassOrInterfaceName::class => Fully\Qualified\ClassName::class,
        ],
        // Use 'invokables' for constructor-less services, or services that do
        // not require arguments to the constructor. Map a service name to the
        // class name.
        'invokables' => [
            // Fully\Qualified\InterfaceName::class => Fully\Qualified\ClassName::class,
        ],
        // Use 'factories' for services provided by callbacks/factory classes.
        'factories' => [
            // Fully\Qualified\ClassName::class => Fully\Qualified\FactoryName::class,
            // \App\ActorSystem\AppActor::class => \App\ActorSystem\ActorSystemFactory::class
            BootAppActor::class => BootAppActorFactory::class,
            \Swoole\Database\PDOProxy::class => MysqlConnectionFactory::class,
            \App\Query\RegistrationUser::class => \App\Query\RegistrationUserFactory::class,
        ],
    ],
];
