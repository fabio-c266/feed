<?php

namespace src\services;

use Exception;
use src\core\JWT;
use src\core\Response;
use src\repositories\UserRepository;

class AuthService
{
    public function __construct(
        private readonly UserRepository $userRepository
    ) {
    }

    public function login(array $data)
    {
        $user = $this->userRepository->findUserByEmail($data['login']) ?? $this->userRepository->findByUsername($data['login']);

        if (!$user) {
            throw new Exception("Login ou senha inválidos.", Response::HTTP_UNAUTHORIZED);
        }

        $isValidPassword = password_verify($data['password'], $user['password']);

        if (!$isValidPassword) {
            throw new Exception("Login ou senha inválidos.", Response::HTTP_UNAUTHORIZED);
        }

        $userData =  [
            "id_public" => $user['id_public'],
            "email" => $user['email']
        ];

        $token = JWT::generate($userData);
        $responsedDta = [
            "token" => $token,
        ];

        return Response::json($responsedDta);
    }
}
