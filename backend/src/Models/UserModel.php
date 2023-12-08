<?php

namespace src\Models;

use Exception;
use src\Config\Database;
use src\Core\Query;

class UserModel
{
    protected string $tableName = 'users';

    public function create(array $data)
    {
        try {
            $queryContent = (new Query(tableName: $this->tableName))
                ->insert(columns: ['id_public', 'username', 'email', 'password'], data: $data)
                ->build();

            Database::query($queryContent);
        } catch (Exception $except) {
            return $except->getMessage();
        }
    }

    public function findUserByEmail(string $email)
    {
        $queryContent = (new Query(tableName: $this->tableName))
            ->select(['id_public', 'email', 'password'])
            ->where('email', $email)
            ->build();

        $user = Database::query($queryContent);
        return $user ? $user[0] : null;
    }

    public function findByUsername(string $username)
    {
        $queryContent = (new Query(tableName: $this->tableName))
            ->select(['id_public', 'username', 'password'])
            ->where('username', $username)
            ->build();

        $user = Database::query($queryContent);
        return $user ? $user[0] : null;
    }

    public function findOne(string $idPublic)
    {
        $queryContent = (new Query(tableName: $this->tableName))
            ->select()
            ->where('id_public', $idPublic)
            ->build();

        $user = Database::query($queryContent);
        return $user ? $user[0] : null;
    }
}
