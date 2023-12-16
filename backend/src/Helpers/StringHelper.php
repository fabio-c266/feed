<?php

namespace src\helpers;

class StringHelper
{
    public static function arrayToQueryValues(array $params): string
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

    public static function arrayToUpdateValues(array $data): string
    {
        $values = array_map(function ($column) use ($data) {
            $value = $data[$column];

            return "{$column} = '{$value}'";
        }, array_keys($data));

        return implode(', ', $values);
    }

    public static function trim(string $string): string
    {
        return str_replace(' ', '', $string);
    }
    public static function getQueryParams(string $url): array
    {
        parse_str($url, $arr);
        return $arr;
    }

    public static function allowImageType(string $name): bool
    {
        $extensions = ['image/png', 'image/jpeg'];
        $isValidImageFormat = false;

        foreach ($extensions as $ext) {
            if (str_contains($name, $ext)) {
                $isValidImageFormat = true;
                break;
            }
        }

        return $isValidImageFormat;
    }
}
