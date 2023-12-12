<?php

namespace src\controllers;

use Exception;
use src\core\JWT;
use src\core\Response;
use src\helpers\ValidationsHelper;
use src\repositories\UserRepository;

class AuthController
{
    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    public function login($req)
    {
        $body = $req['body'];
        $bodySchema = [
            "login" => "string | required",
            "password" => "string | required"
        ];

        try {
            ValidationsHelper::schema(schema: $bodySchema, data: $body);
        } catch (Exception $execpt) {
            throw new Exception($execpt->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        $user = $this->userRepository->findUserByEmail($body['login']) ?? $this->userRepository->findByUsername($body['login']);

        if (!$user) {
            throw new Exception("Login ou senha inválidos.", Response::HTTP_UNAUTHORIZED);
        }

        $isValidPassword = password_verify($body['password'], $user['password']);

        if (!$isValidPassword) {
            throw new Exception("Invalid Credentials.", Response::HTTP_UNAUTHORIZED);
        }

        $data =  [
            "id" => $user['id'],
            "username" => $user['username'],
            "email" => $user['email']
        ];

        $token = JWT::generate($data);
        $data = [
            "token" => $token,
        ];

        return Response::json($data);
    }
}
