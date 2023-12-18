<?php

namespace src\config;

use Exception;
use src\core\Schema;

class Env
{
    public static function validate()
    {
        $envSchema = [
            "DB_HOST" => ['string', 'required'],
            "DB_USER" => ['string', 'required'],
            "DB_PASSWORD" => ['string', 'optional'],
            "DB_NAME" => ['string', 'required'],
            "JWT_SECRET" => ['string', 'required']
        ];

        try {
            (new Schema())->validate(schema: $envSchema, data: $_ENV);
        } catch (Exception $execpt) {
            exit("Invalid environments variables because: \n\n{$execpt->getMessage()}");
        }
    }
}
