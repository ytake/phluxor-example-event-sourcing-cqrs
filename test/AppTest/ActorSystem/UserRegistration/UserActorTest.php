<?php

declare(strict_types=1);

namespace AppTest\ActorSystem\UserRegistration;

use App\ActorSystem\UserRegistration\UserActor;
use App\Command\CreateUser;
use App\Event\ProtoBuf\UserCreated;
use App\Message\UserCreateMessageInterface;
use App\Message\UserCreateResponse;
use Co\WaitGroup;
use Phluxor\ActorSystem;
use PHPUnit\Framework\TestCase;

use function Swoole\Coroutine\run;

class UserActorTest extends TestCase
{
    public function testShouldCallReadModelUpdaterPipeline(): void
    {
        run(function () {
            \Swoole\Coroutine\go(function () {
                $system = ActorSystem::create();
                $c = new ActorSystem\Channel\TypedChannel(
                    $system,
                    fn(mixed $message): bool => $message instanceof UserCreateMessageInterface
                );
                $count = 0;
                $wg = new WaitGroup();
                $wg->add();
                $pipeline = $system->root()->spawn(
                    ActorSystem\Props::fromFunction(
                        new ActorSystem\Message\ReceiveFunction(
                            function (ActorSystem\Context\ContextInterface $context) use (&$count, $wg) {
                                $msg = $context->message();
                                if ($msg instanceof UserCreated) {
                                    $count++;
                                    $this->assertSame('ytake', $msg->getUserName());
                                    $wg->done();
                                }
                            }
                        )
                    )
                );
                $ua = $system->root()->spawn(
                    ActorSystem\Props::fromProducer(fn() => new UserActor($pipeline))
                );
                $system->root()->send($ua, new CreateUser('test1', 'ytake', $c->getRef()));
                $wg->wait();
                $this->assertSame(1, $count);
                $this->assertInstanceOf(UserCreateResponse::class, $c->result());
            });
        });
    }
}
