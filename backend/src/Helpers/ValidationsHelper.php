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
                $dataValue = $data[$schemaKey] ?? (in_array('string', $validationsMethods) ? '' : null);
                $validationMethod = $value;
                $param = '';

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

                if (in_array('nullable', $validationsMethods)) {
                    $classInstance->nullable($dataValue, $param);
                }

                try {
                    call_user_func([$classInstance, $validationMethod], $dataValue, $param);
                } catch (Exception $except) {
                    throw new Exception("{$schemaKey} {$except->getMessage()}");
                }
            }
        }
    }
}
