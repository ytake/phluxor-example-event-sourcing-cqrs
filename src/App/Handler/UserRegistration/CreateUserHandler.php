<?php

declare(strict_types=1);

namespace App\Handler\UserRegistration;

use App\ActorSystem\AppActor;
use App\Command\CreateUser;
use App\Message\UserCreateError;
use App\Message\UserCreateMessageInterface;
use App\Message\UserCreateResponse;
use Laminas\Diactoros\Response\JsonResponse;
use Phluxor\ActorSystem\Channel\TypedChannel;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

readonly class CreateUserHandler implements RequestHandlerInterface
{
    public function __construct(
        private AppActor $appActor
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $params = $request->getParsedBody();
        if (!array_key_exists("email", $params) && !array_key_exists("username", $params)) {
            return new JsonResponse(['message' => 'missing email or username'], 400);
        }
        $system = $this->appActor->system;
        $c = new TypedChannel(
            $system,
            fn(mixed $message): bool => $message instanceof UserCreateMessageInterface
        );
        $system->root()->send(
            $this->appActor->actorRef,
            new CreateUser($params['email'], $params['username'], $c->getRef())
        );
        $result = $c->result();
        return match (true) {
            $result instanceof UserCreateResponse => new JsonResponse(
                ['message' => 'success', 'user' => $result->userID],
                200
            ),
            $result instanceof UserCreateError => new JsonResponse(
                ['message' => 'user already exists'],
                400
            ),
            default => new JsonResponse(['message' => 'failed'], 400),
        };
    }
}
