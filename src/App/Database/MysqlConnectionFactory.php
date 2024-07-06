<?php

declare(strict_types=1);

namespace App\Database;

use Psr\Container\ContainerInterface;
use Swoole\Database\PDOProxy;

class MysqlConnectionFactory
{
    /**
     * @param ContainerInterface $container
     * @return PDOProxy
     */
    public function __invoke(ContainerInterface $container): PDOProxy
    {
        return (new MysqlConnection())->proxy();
    }
}
