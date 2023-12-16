<?php

namespace src\core;

use src\helpers\StringHelper;

class Query
{
    private string $queryContent = '';
    private string $tableName = '';

    public function __construct(string $tableName)
    {
        $this->tableName = $tableName;
    }

    public function select(?array $columns = null)
    {
        $columns = !$columns ? '*' : implode(',', $columns);

        $this->queryContent  = $this->queryContent . "SELECT {$columns} from {$this->tableName} ";
        return $this;
    }

    public function where(string $column, string $value, string $action = '=')
    {
        $this->queryContent = $this->queryContent . "WHERE {$column} {$action} '{$value}' ";
        return $this;
    }

    public function insert(array $columns, array $data)
    {
        $columnsJoined = implode(", ", $columns);
        $values = StringHelper::ArrayToQueryValues($data);

        $this->queryContent = $this->queryContent . "INSERT INTO {$this->tableName} ({$columnsJoined}) VALUES ({$values}) ";
        return $this;
    }

    public function update(array $data)
    {
        $updateValues = StringHelper::arrayToUpdateValues($data);

        $this->queryContent = $this->queryContent . "UPDATE {$this->tableName} SET {$updateValues} ";
        return $this;
    }

    public function delete()
    {
        $this->queryContent = $this->queryContent . "DELETE FROM {$this->tableName} ";
        return $this;
    }

    public function limit(int $limit)
    {
        $this->queryContent = $this->queryContent . "LIMIT {$limit} ";
        return $this;
    }

    public function build()
    {
        return $this->queryContent;
    }
}
