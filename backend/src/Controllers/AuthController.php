<?php

namespace src\controllers;

use Exception;
use src\core\JWT;
use src\core\Response;
use src\helpers\ValidationsHelper;
use src\repositories\UserRepository;
use src\services\AuthService;

class AuthController
{
    public function login($req)
    {
        $body = $req['body'];
        $bodySchema = [
            "login" => "string | required",
            "password" => "string | required"
        ];

        try {
            ValidationsHelper::schema(schema: $bodySchema, data: $body);
            return (new AuthService(new UserRepository()))->login($body);
        } catch (Exception $execpt) {
            throw new Exception($execpt->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
