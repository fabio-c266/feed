<?php

namespace src\services;

use Exception;
use src\core\Response;
use src\core\UUID;
use src\repositories\PostRepository;

class PostService
{
    public function __construct(
        private readonly PostRepository $postRepository,
        private readonly UserService $userService
    ) {
    }

    public function create(array $data)
    {
        $imageName = $data['image_name'] ?? null;
        $userIdPublic = $data['user_id_public'];

        if ($imageName) {
            if (!file_exists("./uploads/{$imageName}")) {
                throw new Exception("Imagem inválida.");
            }

            if ($this->postRepository->findWhere('image_name', $imageName)) {
                throw new Exception("Já possui um post com essa imagem.");
            }
        }

        $user = $this->userService->get($userIdPublic);

        try {
            $newPostData = [
                "id_public" => UUID::generate(),
                "content" => $data['content'],
                "image_name" => $imageName,
                "user_id" => $user['id']
            ];

            $this->postRepository->create($newPostData);

            return $newPostData;
        } catch (Exception $except) {
            throw new Exception('Não foi possível criar a postagem.', Response::HTTP_BAD_REQUEST);
        }
    }

    public function get(string $idPublic)
    {
        $post = $this->postRepository->findOne($idPublic);

        if (!$post) {
            throw new Exception("Não foi possível buscar esse post.", Response::HTTP_BAD_REQUEST);
        }

        return $post;
    }

    public function getAll()
    {
        return $this->postRepository->findMany();
    }

    public function update(string $idPublic, array $data)
    {
        $post = $this->get($idPublic);

        $content = $data['content'] ?? null;
        $imageName = $data['image_name'] ?? null;

        if (!$content && !$imageName) {
            throw new Exception("É necessário inserir por um dado para ser alterado.", Response::HTTP_BAD_REQUEST);
        }

        if ($content) {
            if ($content === $post['content']) {
                throw new Exception("Os contéudos devem ser diferentes.", Response::HTTP_BAD_REQUEST);
            }
        }

        if ($imageName) {
            if (!file_exists("./uploads/{$imageName}")) {
                throw new Exception("Imagem inválida.");
            }

            if ($this->postRepository->findWhere('image_name', $imageName)) {
                throw new Exception("Já possui um post com essa imagem.");
            }
        }

        try {
            $this->postRepository->update($idPublic, $data);
            $newPostdata = $this->postRepository->findOne($idPublic);

            return $newPostdata;
        } catch (Exception $except) {
            throw new Exception($except->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
