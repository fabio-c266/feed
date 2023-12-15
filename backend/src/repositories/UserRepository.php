<?php

namespace src\repositories;

use src\config\Database;
use src\core\Query;

class UserRepository
{
    private string $tableName = 'users';

    public function create(array $data): void
    {
        $queryContent = (new Query(tableName: $this->tableName))
            ->insert(columns: ['id', 'username', 'email', 'password', 'image_id'], data: $data)
            ->build();

        Database::query($queryContent);
    }

    public function findUserByEmail(string $email): array | null
    {
        $queryContent = (new Query(tableName: $this->tableName))
            ->select(['id', 'email', 'username', 'password'])
            ->where('email', $email)
            ->build();

        $user = Database::query($queryContent);
        return $user ? $user[0] : null;
    }

    public function findByUsername(string $username): array | null
    {
        $queryContent = (new Query(tableName: $this->tableName))
            ->select(['id', 'email', 'username', 'password'])
            ->where('username', $username)
            ->build();

        $user = Database::query($queryContent);
        return $user ? $user[0] : null;
    }

    public function findOne(string $id): array | null
    {
        $queryContent = (new Query(tableName: $this->tableName))
            ->select(['id', 'username', 'email', 'image_id', 'created_at'])
            ->where('id', $id)
            ->build();

        $user = Database::query($queryContent);
        return $user ? $user[0] : null;
    }

    public function update(string $id, string $column, $data): void
    {
        $queryContent = (new Query(tableName: $this->tableName))
            ->update($column, $data)
            ->where('id', $id)
            ->limit(1)
            ->build();

        Database::query($queryContent);
    }

    public function delete(string $id): void
    {
        $queryContent = (new Query(tableName: $this->tableName))
            ->delete()
            ->where('id', $id)
            ->limit(1)
            ->build();

        Database::query($queryContent);
    }
}
