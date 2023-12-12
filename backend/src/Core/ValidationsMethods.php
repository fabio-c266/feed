<?php

namespace src\core;

use Exception;

class ValidationsMethods
{
    private bool $isRequired = true;

    public function string($input, $param)
    {
        if (gettype($input) !== 'string' && $this->isRequired) throw new Exception("Precisa ser do tipo string.");
    }

    public function required($value, $param)
    {
        if (!isset($value)) throw new Exception("é obrigatório.");
    }

    public function nullable($input, $param)
    {
        // ONLY INDICATE IF THE DATA IS OPTIONAL  
        $this->isRequired = false;
    }

    public function minLen(string $input, int $minLength)
    {
        if (strlen($input) < $minLength && $this->isRequired) throw new Exception("deve ter no mínimo {$minLength} caracteris.");
    }

    public function maxLen(string $input, int $maxLength)
    {
        if (strlen($input) > $maxLength && $this->isRequired) throw new Exception("deve ter no máximo {$maxLength} caracteris.");
    }

    public function email(string $input, $param)
    {
        $emailRegex = '/^[\w.-]+@([\w-]+\.)+[\w-]{2,4}$/';
        if (!preg_match($emailRegex, $input)) throw new Exception("inválido.");
    }
}
