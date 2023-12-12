<?php

namespace src\controllers;

use Exception;
use src\core\Response;
use src\core\UUID;
use src\helpers\ValidationsHelper;
use src\repositories\UserRepository;

class UserController
{
    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    public function create($req)
    {
        $body = $req['body'];

        $bodySchema = [
            "username" => 'string | required | maxLen:16',
            "email" => 'string | required | email',
            'password' => 'string | required | minLen:6 | maxLen:20',
        ];

        try {
            ValidationsHelper::schema(schema: $bodySchema, data: $body);
        } catch (Exception $execpt) {
            throw new Exception($execpt->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        $username = $body['username'];

        if (strlen($username) > 19) {
            throw new Exception("O username não pode ser maior que 19 caracteris.", Response::HTTP_BAD_REQUEST);
        }

        $email = strtolower(trim($body['email']));

        if ($this->userRepository->findByUsername($username)) {
            throw new Exception("Já existe um usuário com esse username.", Response::HTTP_CONFLICT);
        }

        if ($this->userRepository->findUserByEmail($email)) {
            throw new Exception("Já existe um usuário com esse email.", Response::HTTP_CONFLICT);
        }

        $password = $body['password'];
        $passwordRegex = '/^(?=.*\d).{8,}$/';

        if (!preg_match($passwordRegex, $password)) {
            throw new Exception("A senha deve ter no mínimo 8 caracteris e por o 1 menos um número.", Response::HTTP_BAD_REQUEST);
        }

        $passwordHashed = password_hash($password, PASSWORD_BCRYPT, ["cost" => 6]);

        $data = [
            "id" => UUID::generate(),
            "username" => $username,
            "email" => $email,
            "password" => $passwordHashed,
            "image_id" => null
        ];

        try {
            $this->userRepository->create($data);
            unset($data['password']);

            return Response::json($data);
        } catch (Exception $error) {
            throw new Exception("Não foi possível criar o usuário.", Response::HTTP_BAD_REQUEST);
        }
    }

    public function get($req)
    {
        return Response::json(["message" => "sucesso"]);
    }
}
