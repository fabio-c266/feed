<?php

namespace src\Controllers;

use Exception;
use src\Core\Response;
use src\Core\UUID;
use src\Helpers\ValidationsHelper;
use src\Models\UserModel;

class UserController
{
    public function create($req)
    {
        $body = $req['body'];
        $bodySchema = [
            "username" => ["string"],
            "email" => ['string'],
            'password' => ['string']
        ];

        $isValidSchema = ValidationsHelper::schema(schema: $bodySchema, data: $body);

        if (!$isValidSchema) {
            throw new Exception("Body inválido.", Response::HTTP_BAD_REQUEST);
        }

        $username = $body['username'];

        if (strlen($username) > 19) {
            throw new Exception("", Response::HTTP_BAD_REQUEST);
        }

        $email = strtolower(trim($body['email']));
        $userModel = new UserModel();

        if ($userModel->findByUsername($username)) {
            throw new Exception("Já existe um usuário com esse username.", Response::HTTP_CONFLICT);
        }

        if ($userModel->findUserByEmail($email)) {
            throw new Exception("Já existe um usuário com esse email.", Response::HTTP_CONFLICT);
        }

        $password = $body['password'];
        $passwordRegex = '/^(?=.*\d).{8,}$/';

        if (!preg_match($passwordRegex, $password)) {
            throw new Exception("A senha deve ter no mínimo 8 caracteris e por o 1 menos um número.", Response::HTTP_BAD_REQUEST);
        }

        $passwordHashed = password_hash($password, PASSWORD_BCRYPT, ["cost" => 6]);

        $data = [
            "id_public" => UUID::generate(),
            "username" => $username,
            "email" => $email,
            "password" => $passwordHashed,
            "image_id" => null
        ];

        try {
            $userModel->create($data);
        } catch (Exception $error) {
            throw new Exception("Não foi possível criar o usuário.", Response::HTTP_BAD_REQUEST);
        }
    }
}
