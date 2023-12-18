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

        if (strlen($username) > 16) {
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

        $userData = [
            "id_public" => UUID::generate(),
            "username" => $username,
            "email" => $email,
            "password" => $passwordHashed
        ];

        try {
            $this->userRepository->create($userData);
            unset($userData['password']);

            return $data;
        } catch (Exception) {
            throw new Exception("Não foi possível criar o usuário.", Response::HTTP_BAD_REQUEST);
        }
    }

    public function get(string $idPublic)
    {
        $user = $this->userRepository->findOne($idPublic);

        if (!$user) {
            throw new Exception("Não foi possível buscar suas informações.", Response::HTTP_BAD_REQUEST);
        }

        return $user;
    }

    public function update(string $idPublic, array $data)
    {
        try {
            $user = $this->get($idPublic);

            $newUsername = $data['username'] ?? null;
            $avatarName = $data['avatar_name'] ?? null;

            if (!$newUsername && !$avatarName) {
                throw new Exception("É necessário no mínimo um dado para ser atualizado.");
            }

            if ($newUsername) {
                if ($user['username'] === $newUsername) {
                    throw new Exception("Você não pode alterar para o mesmo nome de usuário.");
                }

                $someUsingUsername = $this->userRepository->findByUsername($newUsername);

                if ($someUsingUsername) {
                    throw new Exception("Já existe alguém com esse nome de usuário.");
                }
            }

            if ($avatarName) {
                if (!file_exists("./uploads/{$avatarName}")) {
                    throw new Exception("Imagem inválida.");
                }

                if ($this->userRepository->findWhere('avatar_name', $avatarName)) {
                    throw new Exception("Já possui um usuário com esse avatar.");
                }
            }

            $this->userRepository->update($idPublic, $data);

            $newUserdata = $this->userRepository->findOne($idPublic);

            return $newUserdata;
        } catch (Exception $except) {
            throw new Exception($except->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
