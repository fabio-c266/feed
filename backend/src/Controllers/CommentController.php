<?php

namespace src\controllers;

use Exception;
use src\core\Response;
use src\core\Schema;
use src\repositories\CommentRepository;
use src\repositories\PostRepository;
use src\repositories\UserRepository;
use src\services\CommentService;
use src\services\PostService;
use src\services\UserService;

class CommentController
{
    public function create($req)
    {
        $jwt = $req['jwt_data'];
        $jwtData = $jwt->data;
        $bodySchema = [
            "content" => ["string", "required", "minLen:1"],
            "post_id_public" => ["string", "required", "minLen: 1"],
        ];

        try {
            $body = (new Schema())->validate(schema: $bodySchema, data: $req['body']);
            $body['user_id_public'] = $jwtData->id_public;

            $userService = new UserService(new UserRepository());
            $responseData = (new CommentService(
                new CommentRepository(),
                $userService,
                new PostService(new PostRepository(), $userService),
            ))->create($body);

            return Response::json($responseData, Response::HTTP_CREATED);
        } catch (Exception $execpt) {
            throw new Exception($execpt->getMessage(), $execpt->getCode() === 0 ? Response::HTTP_BAD_REQUEST : $execpt->getCode());
        }
    }

    public function getAll($req)
    {
        $queryParamentsSchema = [
            "post_id_public" => ["string", "required", "minLen: 1"],
        ];

        try {
            $queryParaments = (new Schema())->validate(schema: $queryParamentsSchema, data: $req['query']);

            $userService = new UserService(new UserRepository());
            $responseData = (new CommentService(
                new CommentRepository(),
                $userService,
                new PostService(new PostRepository(), $userService),
            ))->getAllByPostId($queryParaments['post_id_public']);

            return Response::json($responseData);
        } catch (Exception $execpt) {
            throw new Exception($execpt->getMessage(), $execpt->getCode() === 0 ? Response::HTTP_BAD_REQUEST : $execpt->getCode());
        }
    }

    public function update($req)
    {
        $jwt = $req['jwt_data'];
        $jwtData = $jwt->data;

        $queryParamentsSchema = [
            "comment_id_public" => ["string", "required", "minLen: 1"],
        ];

        $bodySchema = [
            "content" => ["string", "required", "minLen: 1"]
        ];

        try {
            $queryParaments = (new Schema())->validate(schema: $queryParamentsSchema, data: $req['query']);
            $body = (new Schema())->validate(schema: $bodySchema, data: $req['body']);
            $body['user_id_public'] = $jwtData->id_public;

            $userService = new UserService(new UserRepository());
            $responseData = (new CommentService(
                new CommentRepository(),
                $userService,
                new PostService(new PostRepository(), $userService),
            ))->update($queryParaments['comment_id_public'], $body);

            return Response::json($responseData);
        } catch (Exception $execpt) {
            throw new Exception($execpt->getMessage(), $execpt->getCode() === 0 ? Response::HTTP_BAD_REQUEST : $execpt->getCode());
        }
    }

    public function delete($req)
    {
        $jwt = $req['jwt_data'];
        $jwtData = $jwt->data;

        $queryParamentsSchema = [
            "comment_id_public" => ["string", "required", "minLen: 1"],
        ];

        try {
            $queryParaments = (new Schema())->validate(schema: $queryParamentsSchema, data: $req['query']);

            $userService = new UserService(new UserRepository());
            (new CommentService(
                new CommentRepository(),
                $userService,
                new PostService(new PostRepository(), $userService),
            ))->delete($queryParaments['comment_id_public'], $jwtData->id_public);

            return Response::json([], Response::HTTP_OK);
        } catch (Exception $execpt) {
            throw new Exception($execpt->getMessage(), $execpt->getCode() === 0 ? Response::HTTP_BAD_REQUEST : $execpt->getCode());
        }
    }
}
