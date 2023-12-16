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
            ->insert(columns: ['id_public', 'username', 'email', 'password'], data: $data)
            ->build();

        Database::query($queryContent);
    }

    public function findUserByEmail(string $email): array | null
    {
        $queryContent = (new Query(tableName: $this->tableName))
            ->select(['id_public', 'email', 'username', 'password'])
            ->where('email', $email)
            ->build();

        $user = Database::query($queryContent);
        return $user ? $user[0] : null;
    }

    public function findByUsername(string $username): array | null
    {
        $queryContent = (new Query(tableName: $this->tableName))
            ->select(['id_public', 'email', 'username', 'password'])
            ->where('username', $username)
            ->build();

        $user = Database::query($queryContent);
        return $user ? $user[0] : null;
    }

    public function findOne(string $idPublic): array | null
    {
        $queryContent = (new Query(tableName: $this->tableName))
            ->select(['id_public', 'username', 'email', 'image', 'created_at'])
            ->where('id_public', $idPublic)
            ->build();

        $user = Database::query($queryContent);
        return $user ? $user[0] : null;
    }

    public function update(string $idPublic, array $data): void
    {
        $queryContent = (new Query(tableName: $this->tableName))
            ->update($data)
            ->where('id_public', $idPublic)
            ->limit(1)
            ->build();

        Database::query($queryContent);
    }

    public function delete(string $idPublic): void
    {
        $queryContent = (new Query(tableName: $this->tableName))
            ->delete()
            ->where('id_public', $idPublic)
            ->limit(1)
            ->build();

        Database::query($queryContent);
    }
}
