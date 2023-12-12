<?php

namespace src\config;

use Exception;
use src\helpers\ValidationsHelper;

class Env
{
    public static function validate()
    {
        $envSchema = [
            "DB_HOST" =>     'string | required',
            "DB_USER" =>     'string | required',
            "DB_PASSWORD" => 'string | nullable',
            "DB_NAME" =>     'string | required',
            "JWT_SECRET" =>  'string | required'
        ];

        try {
            ValidationsHelper::schema(schema: $envSchema, data: $_ENV);
        } catch (Exception $execpt) {
            exit("Invalid invoriments variables becausa: \n\n{$execpt->getMessage()}");
        }
    }
}
