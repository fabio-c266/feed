<?php

namespace src\services;

use Exception;
use src\core\Response;
use src\core\UUID;
use src\repositories\UserRepository;

class UserService
{
    public function __construct(
        private readonly UserRepository $userRepository
    ) {
    }

    public function create(array $data)
    {
        $username = $data['username'];

        if (strlen($username) > 19) {
            throw new Exception("O username não pode ser maior que 19 caracteris.", Response::HTTP_BAD_REQUEST);
        }

        $email = strtolower(trim($data['email']));

        if ($this->userRepository->findByUsername($username)) {
            throw new Exception("Já existe um usuário com esse username.", Response::HTTP_CONFLICT);
        }

        if ($this->userRepository->findUserByEmail($email)) {
            throw new Exception("Já existe um usuário com esse email.", Response::HTTP_CONFLICT);
        }

        $password = $data['password'];
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

            return Response::json($data, Response::HTTP_CREATED);
        } catch (Exception $error) {
            throw new Exception("Não foi possível criar o usuário.", Response::HTTP_BAD_REQUEST);
        }
    }

    public function get(string $id)
    {
        $user = $this->userRepository->findOne($id);

        if (!$user) {
            throw new Exception("Não foi possível buscar suas informações.", Response::HTTP_BAD_REQUEST);
        }

        return Response::json($user);
    }

    public function update(string $id, array $data)
    {
    }

    public function delete(string $id)
    {
        try {
            $user = $this->get($id);
            $this->userRepository->delete($user['id']);

            return Response::status(Response::HTTP_OK);
        } catch (Exception $error) {
            throw new Exception($error->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
