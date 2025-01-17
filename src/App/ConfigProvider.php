<?php

declare(strict_types=1);

namespace App;

use App\Handler\UserRegistration\CreateUserHandler;
use App\Handler\UserRegistration\CreateUserHandlerFactory;
use App\Handler\UserRegistration\ListHandler;
use App\Handler\UserRegistration\ListHandlerFactory;
use Mezzio\Helper\BodyParams\BodyParamsMiddleware;

/**
 * The configuration provider for the App module
 *
 * @see https://docs.laminas.dev/laminas-component-installer/
 */
class ConfigProvider
{
    /**
     * Returns the configuration array
     *
     * To add a bit of a structure, each section is defined in a separate
     * method which returns an array with its configuration.
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'templates'    => $this->getTemplates(),
        ];
    }

    /**
     * Returns the container dependencies
     */
    public function getDependencies(): array
    {
        return [
            'invokables' => [
                BodyParamsMiddleware::class => BodyParamsMiddleware::class,
            ],
            'factories'  => [
                ListHandler::class => ListHandlerFactory::class,
                CreateUserHandler::class => CreateUserHandlerFactory::class,
            ],
        ];
    }

    /**
     * Returns the templates configuration
     */
    public function getTemplates(): array
    {
        return [
            'paths' => [
                'app'    => ['templates/app'],
                'error'  => ['templates/error'],
                'layout' => ['templates/layout'],
            ],
        ];
    }
}
