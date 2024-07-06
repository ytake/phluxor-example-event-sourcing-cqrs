<?php

declare(strict_types=1);

namespace AppTest\ActorSystem;

use App\Command\CreateUser;
use App\Message\UserCreateError;
use App\Message\UserCreateMessageInterface;
use App\Message\UserCreateResponse;
use Phluxor\ActorSystem;
use PHPUnit\Framework\TestCase;

use function Swoole\Coroutine\run;

class RestApiActorTest extends TestCase
{
    use BootActorTrait;

    public function testShouldReturnUserCreateMessage(): void
    {
        run(function () {
            \Swoole\Coroutine\go(function () {
                $system = ActorSystem::create();
                $c = new ActorSystem\Channel\TypedChannel(
                    $system,
                    fn(mixed $message): bool => $message instanceof UserCreateMessageInterface
                );
                $result = $this->bootActor($system);
                $system->root()->send($result->getRef(), new CreateUser('test', 'test', $c->getRef()));
                $this->assertInstanceOf(UserCreateResponse::class, $c->result());
                $system->root()->send($result->getRef(), new CreateUser('test', 'test', $c->getRef()));
                $this->assertInstanceOf(UserCreateError::class, $c->result());

                // persistenceを利用するアクターのアドレスを指定して停止させる
                $r = $system->root()->stopFuture(
                    new ActorSystem\Ref(
                        new ActorSystem\ProtoBuf\Pid([
                            'id' => 'rest_api/user:test',
                            'address' => ActorSystem::LOCAL_ADDRESS,
                        ])
                    )
                );
                $r->wait();
                // 再度同じメッセージを送信し、persistence利用のアクターを再起動させて状態を変更しない・アクター生成済みであることを通知する
                $system->root()->send($result->getRef(), new CreateUser('test', 'test', $c->getRef()));
                $this->assertInstanceOf(UserCreateError::class, $c->result());
            });
        });
    }
}
