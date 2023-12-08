<?php

namespace src\Helpers;

class StringHelper 
{
    public static function ArrayToQueryValues(array $params)
    {
        $formattedParams = array_map(function ($value) {
            return "'" . addslashes($value) . "'";
        }, $params);

        return implode(', ', $formattedParams);
    }
}