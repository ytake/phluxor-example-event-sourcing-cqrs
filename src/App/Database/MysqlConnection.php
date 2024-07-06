<?php

declare(strict_types=1);

namespace App\Database;

use Phluxor\Persistence\Mysql\Connection;
use Phluxor\Persistence\Mysql\Dsn;
use Swoole\Database\PDOProxy;

class MysqlConnection
{
    public function proxy(): PDOProxy
    {
        $conn = new Connection(
            new Dsn(
                '127.0.0.1',
                3306,
                'sample',
                'user',
                'passw@rd'
            )
        );
        return $conn->proxy();
    }
}