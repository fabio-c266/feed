<?php

namespace src\repositories;

use src\config\Database;
use src\core\Query;

class ImageRepository
{
    private string $tableName = 'images';

    public function create(array $data): void
    {
        $queryContent = (new Query(tableName: $this->tableName))
            ->insert(columns: ['id', 'original_name', 'new_name'], data: $data)
            ->build();

        Database::query($queryContent);
    }

    public function findOne(string $id)
    {
        $queryContent = (new Query(tableName: $this->tableName))
            ->select()
            ->where('id', $id)
            ->build();

        $image = Database::query($queryContent);
        return $image ? $image[0] : null;
    }

    public function update(string $id, array $data)
    {
        $queryContent = (new Query(tableName: $this->tableName))
            ->update($data)
            ->where('id', $id)
            ->limit(1)
            ->build();

        Database::query($queryContent);
    }

    public function delete(string $id)
    {
        $queryContent = (new Query(tableName: $this->tableName))
            ->delete()
            ->where('id', $id)
            ->limit(1)
            ->build();

        Database::query($queryContent);
    }
}
