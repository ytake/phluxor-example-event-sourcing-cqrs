<?php

declare(strict_types=1);

namespace App\Handler\UserRegistration;

use App\Query\RegistrationUser;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

readonly class ListHandler implements RequestHandlerInterface
{
    public function __construct(
        private RegistrationUser $registrationUser,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $result = $this->registrationUser->findAll();
        return match (true) {
            $result !== null => new JsonResponse($result, 200),
            default => new JsonResponse(['message' => 'unknown'], 400),
        };
    }
}
