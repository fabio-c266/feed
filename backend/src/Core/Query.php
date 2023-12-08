<?php

namespace src\Core;

use src\Helpers\StringHelper;

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
        
        $this->queryContent." SELECT {$columns} from {$this->tableName}";
        return $this;
    }

    public function where(string $column, string $value, string $action = '=')
    {
        $this->queryContent." WHERE {$column} {$action} '{$value}'";
        return $this;
    }

    public function insert(array $columns, array $data) 
    {
        $columnsJoined = implode(", ", $columns);
        $values = StringHelper::ArrayToQueryValues($data);

        $this->queryContent. " INSERT INTO {$this->tableName} ({$columnsJoined}) VALUES ({$values})";
        return $this;
    }

    public function build() 
    {
        return $this->queryContent;
    }
}