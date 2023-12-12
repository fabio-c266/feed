<?php

namespace src\helpers;

use Exception;

class ValidationsHelper
{
    public static function schema(array $schema, array $data)
    {
        foreach ($schema as $schemaKey => $schemaValue) {
            if (gettype($schemaValue) != 'string') throw new Exception("Invalid schema value in {$schemaKey}. Use validation1 | validation2 ...");

            $validationsString = StringHelper::trim($schemaValue);
            $validationsMethods = str_contains($validationsString, '|') ? explode('|', $validationsString) : [$validationsString];

            foreach ($validationsMethods as $value) {
                $dataValue = $data[$schemaKey] ?? null;
                $validationMethod = $value;
                $param = '';
                $allowFailType = in_array('nullable', $validationsMethods) ? true : false;

                if (in_array('string', $validationsMethods) && str_contains($value, ':')) {
                    [$stringMethod, $stringValue] = explode(':', $value);

                    $validationMethod = $stringMethod;
                    $param = (int)$stringValue;
                }

                $class = "src\\core\\ValidationsMethods";
                $classInstance = new $class();

                if (!method_exists($classInstance, $validationMethod)) {
                    throw new Exception("Invalid value {$value} in {$schemaKey}.");
                }

                try {
                    call_user_func([$classInstance, $validationMethod], $dataValue, $param, $allowFailType);
                } catch (Exception $except) {
                    throw new Exception("{$schemaKey} {$except->getMessage()}");
                }
            }
        }
    }
}
