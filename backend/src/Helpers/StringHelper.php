<?php

namespace src\helpers;

class StringHelper
{
    public static function ArrayToQueryValues(array $params): string
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

    public static function trim(string $string): string
    {
        return str_replace(' ', '', $string);
    }
    public static function getQueryParams(string $url): array
    {
        parse_str($url, $arr);
        return $arr;
    }

    public static function allowImageType(string $name): bool {
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
