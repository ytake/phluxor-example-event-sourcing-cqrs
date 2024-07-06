<?php

declare(strict_types=1);

namespace AppTest\ActorSystem\UserRegistration;

use App\ActorSystem\UserRegistration\ReadModelUpdateActor;
use App\Database\MysqlConnection;
use App\Database\RegisteredUser;
use App\Event\ProtoBuf\UserCreated;
use App\Query\RegistrationUser;
use Phluxor\ActorSystem;
use PHPUnit\Framework\TestCase;
use Swoole\Database\PDOConfig;
use Swoole\Database\PDOPool;
use Symfony\Component\Uid\Ulid;

use function Swoole\Coroutine\run;

class ReadModelUpdateActorTest extends TestCase
{
    public function tearDown(): void
    {
        run(function () {
            \Swoole\Coroutine\go(function () {
                $pool = new PDOPool(
                    (new PDOConfig())
                        ->withHost('127.0.0.1')
                        ->withPort(3306)
                        ->withDbName('sample')
                        ->withCharset('utf8mb4')
                        ->withUsername('user')
                        ->withPassword('passw@rd')
                );
                $pool->get()->exec('TRUNCATE journals;');
                $pool->get()->exec('TRUNCATE snapshots;');
                $pool->get()->exec('TRUNCATE users;');
                $pool->close();
            });
        });
    }

    public function testShouldAddUser(): void
    {
        run(function () {
            \Swoole\Coroutine\go(function () {
                $system = ActorSystem::create();
                $conn = new MysqlConnection();
                $rmu = $system->root()->spawn(
                    ActorSystem\Props::fromProducer(
                        fn() => new ReadModelUpdateActor(new RegisteredUser($conn->proxy()))
                    )
                );
                $ev = new UserCreated([
                    'userID' => Ulid::generate(),
                    'email' => 'test@ytake.com',
                    'userName' => 'ytake'
                ]);
                $system->root()->send($rmu, $ev);
                sleep(1);
                $query = new RegistrationUser($conn->proxy());
                $this->assertCount(1, $query->findAll());
            });
        });
    }
}
