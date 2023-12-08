<?php

namespace src\Helpers;

use Exception;

class ValidationsHelper
{
    public static function schema(array $schema, array $data)
    {
        $avaliableTypes = [
            "string",
            "int",
            "float",
            "array",
            "object",
            "optinal"
        ];

        foreach ($schema as $schemaKey => $schemaValue) {
            $equalsValues = array_intersect($avaliableTypes, $schemaValue);
            if (count($equalsValues) < 1) return new Exception("Invalid schema data in {$schemaKey}.");

            if (in_array('optinal', $schemaValue) && !array_key_exists($schemaKey, $data)) return true;
            $newSchemaValue = array_filter($schemaValue, function ($value) {
                return $value != 'optinal';
            });

            return self::validateType(types: $newSchemaValue, value: $data[$schemaKey]);
        }

        return true;
    }

    public static function validateType(array $types, $value)
    {
        foreach ($types as $type) {
            $dataValueType = gettype($value);

            if ($dataValueType === "string" && $value === '') return false;
            return $dataValueType === $type;
        }
    }
}
