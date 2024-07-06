<?php

declare(strict_types=1);

use App\Handler\UserRegistration\CreateUserHandler;
use App\Handler\UserRegistration\ListHandler;
use Mezzio\Application;
use Mezzio\MiddlewareFactory;
use Psr\Container\ContainerInterface;

return static function (Application $app, MiddlewareFactory $factory, ContainerInterface $container): void {
    // Register the AppActor in the container
    $app->get('/', App\Handler\HomePageHandler::class, 'home');
    $app->post('/user/registration', CreateUserHandler::class, 'api.create-user');
    $app->get('/users', ListHandler::class, 'api.list-users');
};
