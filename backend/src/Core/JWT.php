<?php

namespace src\Core;

use Exception;
use Firebase\JWT\JWT as jwtLib;
use Firebase\JWT\Key as jwtLibKey;

class JWT
{
    public static function generate($data)
    {
        $payload = [
            "exp" => time() + 20, //6 Hours 3600 * 6
            "iat" => time(),
            "data" => $data
        ];

        $token = jwtLib::encode($payload, $_ENV['JWT_SECRET'], 'HS256');
        return $token;
    }

    public static function get_data($token)
    {
        try {
            $data = jwtLib::decode($token, new jwtLibKey($_ENV['JWT_SECRET'], 'HS256'));
            return $data;
        } catch (Exception $error) {
            return false;
        }
    }
}