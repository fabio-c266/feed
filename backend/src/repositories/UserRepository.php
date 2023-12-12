<?php

namespace src\repositories;

use src\config\Database;
use src\core\Query;

class UserRepository
{
    private string $tableName = 'users';

    public function create(array $data)
    {
        $queryContent = (new Query(tableName: $this->tableName))
            ->insert(columns: ['id', 'username', 'email', 'password', 'image_id'], data: $data)
            ->build();

        Database::query($queryContent);
    }

    public function findUserByEmail(string $email)
    {
        $queryContent = (new Query(tableName: $this->tableName))
            ->select(['id', 'email', 'password'])
            ->where('email', $email)
            ->build();

        $user = Database::query($queryContent);
        return $user ? $user[0] : null;
    }

    public function findByUsername(string $username)
    {
        $queryContent = (new Query(tableName: $this->tableName))
            ->select(['id', 'username', 'password'])
            ->where('username', $username)
            ->build();

        $user = Database::query($queryContent);
        return $user ? $user[0] : null;
    }

    public function findOne(string $id)
    {
        $queryContent = (new Query(tableName: $this->tableName))
            ->select()
            ->where('id', $id)
            ->build();

        $user = Database::query($queryContent);
        return $user ? $user[0] : null;
    }
}
