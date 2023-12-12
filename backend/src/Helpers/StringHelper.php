<?php

namespace src\helpers;

class StringHelper
{
    public static function ArrayToQueryValues(array $params)
    {
        $formattedParams = array_map(function ($value) {
            if ($value === null) {
                return 'NULL';
            } else {
                return "'" . addslashes($value) . "'";
            }
        }, $params);

        return implode(', ', $formattedParams);
    }


    public static function trim(string $string)
    {
        return str_replace(' ', '', $string);
    }
}
