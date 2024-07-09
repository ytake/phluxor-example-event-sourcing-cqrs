<?php

declare(strict_types=1);

namespace App\Database;

use PDO;
use Swoole\Database\PDOProxy;

readonly class RegisteredUser
{
    public function __construct(
        private PDOProxy $proxy
    ) {
    }

    public function findByEmail(string $email): mixed
    {
        $stmt = $this->proxy->prepare('SELECT id, name, email FROM users WHERE email = ?');
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addUser(string $id, string $name, string $email): mixed
    {
        $this->proxy->beginTransaction();
        $stmt = $this->proxy->prepare('INSERT INTO users (id, name, email) VALUES (?, ?, ?)');
        $result = $stmt->execute([$id, $name, $email]);
        $result === false ? $this->proxy->rollBack() : $this->proxy->commit();
        return $result;
    }
}
