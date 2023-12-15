<?php

namespace src\controllers;

use Exception;
use src\core\Response;
use src\core\UUID;
use src\helpers\ValidationsHelper;
use src\repositories\ImageRepository;
use src\repositories\UserRepository;
use src\services\UserService;

class UserController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly ImageRepository $imageRepository
    ) {
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
            return (new UserService($this->userRepository))->create($body);
        } catch (Exception $execpt) {
            throw new Exception($execpt->getMessage(), $execpt->getCode() === 0 ? Response::HTTP_BAD_REQUEST : $execpt->getCode());
        }
    }

    public function get($req)
    {
        $jwt = $req['jwt_data'];
        $jwtData = $jwt->data;

        try {
            return (new UserService($this->userRepository))->get($jwtData->id);
        } catch (Exception $execpt) {
            throw new Exception($execpt->getMessage(), $execpt->getCode() === 0 ? Response::HTTP_BAD_REQUEST : $execpt->getCode());
        }
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
            } catch (Exception $execpt) {
                throw new Exception("Não foi possível alterar o username.", Response::HTTP_BAD_REQUEST);
            }
        }

        if ($image_id) {
            $image = $this->imageRepository->findOne($image_id);

            if (!$image) {
                throw new Exception("Imagem inválida.", Response::HTTP_BAD_REQUEST);
            }

            $user = $this->userRepository->findOne($jwtData->id);

            if (!$user) {
                throw new Exception("Usuário inválido", Response::HTTP_BAD_REQUEST);
            }

            if ($image_id === $user['image_id']) {
                throw new Exception("O usuário já possui essa imagem de perfil.", Response::HTTP_BAD_REQUEST);
            }

            try {
                if ($user['image_id']) {
                    $currentUserImage = $this->imageRepository->findOne($user['image_id']);

                    $this->imageRepository->update($currentUserImage['id'], 'original_name', $image['original_name']);
                    $this->imageRepository->update($currentUserImage['id'], 'new_name', $image['new_name']);

                    $this->imageRepository->delete($image['id']);
                    $imageToDeletePath = "./uploads/{$currentUserImage['new_name']}";

                    if (file_exists($imageToDeletePath)) {
                        unlink($imageToDeletePath);
                    }
                } else {
                    $this->userRepository->update($user['id'], 'image_id', $image_id);
                }
            } catch (Exception $execpt) {
                print($execpt->getMessage());
                throw new Exception("Não foi possível alterar a imagem.", Response::HTTP_BAD_REQUEST);
            }
        }

        $newUserData = $this->userRepository->findOne($jwtData->id);
        return Response::json($newUserData);
    }
}
