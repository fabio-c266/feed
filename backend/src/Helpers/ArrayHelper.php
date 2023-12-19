<?php

namespace src\helpers;

class ArrayHelper
{
    public static function formatData(array $data): array
    {
        $dataFormated = [];

        foreach ($data as $key => $value) {
            foreach ($value as $column => $columnValue) {
                if (str_contains($column, '.')) {
                    [$subColumn, $columnKey] = explode('.', $column);

                    $dataFormated[$key][$subColumn][$columnKey] = $columnValue;
                } else {
                    $dataFormated[$key][$column] = $columnValue;
                }
            }
        }

        return $dataFormated;
    }
}
