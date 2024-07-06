<?php

declare(strict_types=1);

namespace App\Query;

use PDO;
use Swoole\Database\PDOProxy;

readonly class RegistrationUser
{
    public function __construct(
        private PDOProxy $proxy
    ) {
    }

    public function findAll(): mixed
    {
        $stmt = $this->proxy->prepare('SELECT id, name, email, created_at FROM users ORDER BY created_at DESC');
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
