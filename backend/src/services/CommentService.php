<?php

namespace src\services;

use Exception;
use src\core\Response;
use src\core\UUID;
use src\repositories\CommentRepository;

class CommentService
{
    public function __construct(
        private readonly CommentRepository $commentRepository,
        private readonly UserService $userService,
        private readonly PostService $postService
    ) {
    }

    public function create(array $data)
    {
        $userIdPublic = $data['user_id_public'];
        $postIdPublic = $data['post_id_public'];

        $user = $this->userService->get($userIdPublic);
        $post = $this->postService->get($postIdPublic);

        try {
            $newCommentData = [
                "id_public" => UUID::generate(),
                "content" => $data['content'],
                "user_id" => $user['id'],
                "post_id" => $post['id']
            ];

            $this->commentRepository->create($newCommentData);

            return $newCommentData;
        } catch (Exception $except) {
            throw new Exception('Não foi possível fazer esse comentário.', Response::HTTP_BAD_REQUEST);
        }
    }

    public function get(string $idPublic)
    {
        $comment = $this->commentRepository->findOne($idPublic);

        if (!$comment) {
            throw new Exception("Não foi possível buscar esse comentário.", Response::HTTP_BAD_REQUEST);
        }

        return $comment;
    }

    public function getAllByPostId(string $idPublic)
    {
        $post = $this->postService->get($idPublic);
        return $this->commentRepository->findManyByPostId($post['id']);
    }

    public function update(string $idPublic, array $data)
    {
        $comment = $this->get($idPublic);
        $user = $this->userService->get($data['user_id_public']);

        if ($comment['user_id'] !== $user['id']) {
            throw new Exception("Apenas o dono dessa comentário pode apaga-lo.", Response::HTTP_BAD_REQUEST);
        }

        $newCommentContent = $data['content'];

        if ($comment['content'] === $newCommentContent) {
            throw new Exception("O novo contéudo deve ser diferente do atual.", Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->commentRepository->update(
                $comment['id_public'],
                ["content" => $newCommentContent]
            );
            return $this->commentRepository->findOne($comment['id_public']);
        } catch (Exception $except) {
            throw new Exception('Não foi possível editar esse comentário.', Response::HTTP_BAD_REQUEST);
        }
    }

    public function delete(string $idPublic, string $userIdPublic): void
    {
        $comment = $this->get($idPublic);
        $user = $this->userService->get($userIdPublic);

        if ($comment['user_id'] !== $user['id']) {
            throw new Exception("Apenas o dono desse comentário pode apaga-lo.", Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->commentRepository->delete($comment['id_public']);
        } catch (Exception $except) {
            throw new Exception('Não foi possível excluir esse comentário.', Response::HTTP_BAD_REQUEST);
        }
    }
}
