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
            "username" => 'string | required | minLen:6 | maxLen:16',
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
        $jwt = $req['jwt_data'];
        $jwtData = $jwt->data;

        $user = $this->userRepository->findOne($jwtData->id);

        if (!$user) {
            throw new Exception("Não foi possível buscar suas informações.", Response::HTTP_BAD_REQUEST);
        }

        return Response::json($user);
    }

    public function update($req)
    {
        $jwt = $req['jwt_data'];
        $jwtData = $jwt->data;

        $body = $req['body'];
        $bodySchema = [
            "username" => 'string | nullable | minLen:6 | maxLen:16',
            "image_id" => 'string | nullable | maxLen:255'
        ];

        try {
            ValidationsHelper::schema(schema: $bodySchema, data: $body);
        } catch (Exception $execpt) {
            throw new Exception($execpt->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        $username = $body['username'] ?? '';
        $image_id = $body['image_id'] ?? '';

        if (!$username && !$image_id) {
            throw new Exception("É necessário por o menos o username ou o image_id.", Response::HTTP_BAD_REQUEST);
        }

        if ($username) {
            $user = $this->userRepository->findByUsername($username);

            if ($user) {
                throw new Exception("Já existe um usuário com esse username.");
            }

            try {
                $this->userRepository->update($jwtData->id, 'username', $username);
                $newUserData = $this->userRepository->findOne($jwtData->id);

                return Response::json($newUserData);
            } catch (Exception $execpt) {
                throw new Exception("Não foi possível alterar o username.", Response::HTTP_BAD_REQUEST);
            }
        }

        if ($image_id) {
        }
    }
}
