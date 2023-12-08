<?php

namespace src\Config;

use src\Helpers\ValidationsHelper;

class Env 
{
    public static function validate() 
    {
        $envSchema = [
            "DB_HOST" => ["string"],
            "DB_USER" => ["string"],
            "DB_PASSWORD" => ["string"],
            "DB_NAME" => ["string"],
            "JWT_SECRET" => ["string"]
        ];

        $isValidEnvData = ValidationsHelper::schema(schema: $envSchema, data: $_ENV);

        if (!$isValidEnvData) {
            exit('Invalid invoriments variables.');
        }
    }
}