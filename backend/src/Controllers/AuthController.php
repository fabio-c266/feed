<?php

namespace src\controllers;

use Exception;
use src\core\IRequest;
use src\core\Response;
use src\core\Schema;
use src\repositories\UserRepository;
use src\services\AuthService;

class AuthController
{
    public function login($req)
    {
        $bodySchema = [
            "login" => ["string", "required"],
            "password" => ["string", "required"]
        ];

        try {
            $body = (new Schema())->validate(schema: $bodySchema, data: $req['body']);
            $responseData = (new AuthService(new UserRepository()))->login($body);

            return Response::json($responseData);
        } catch (Exception $execpt) {
            throw new Exception($execpt->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
