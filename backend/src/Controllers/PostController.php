<?php

namespace src\controllers;

use Exception;
use src\core\Response;
use src\core\Schema;
use src\repositories\PostRepository;
use src\repositories\UserRepository;
use src\services\PostService;
use src\services\UserService;

class PostController {
    public function create($req) {
        $jwt = $req['jwt_data'];
        $jwtData = $jwt->data;
        $bodySchema = [
            "content" => ["string", "required", "minLen:1"],
            "image_name" => ["string", "optional"],
        ];

        try {
            $body = (new Schema())->validate(schema: $bodySchema, data: $req['body']);
            $body['user_id_public'] = $jwtData->id_public;
            $responseData = (new PostService(
                new PostRepository(),
                new UserService(new UserRepository()),
            ))->create($body);

            return Response::json($responseData, Response::HTTP_CREATED);
        } catch (Exception $execpt) {
            throw new Exception($execpt->getMessage(), $execpt->getCode() === 0 ? Response::HTTP_BAD_REQUEST : $execpt->getCode());
        }
    }

    public function get($req) {
        $queryParamentsSchema = [
            "id_public" => ["string", "required"]
        ];

        try {
            $query = (new Schema())->validate(schema: $queryParamentsSchema, data: $req['query']);
            $responseData = (new PostService(
                new PostRepository(),
                new UserService(new UserRepository()),
            ))->get($query['id_public']);

            return Response::json($responseData);
        } catch (Exception $execpt) {
            throw new Exception($execpt->getMessage(), $execpt->getCode() === 0 ? Response::HTTP_BAD_REQUEST : $execpt->getCode());
        }
    }

    public function getAll($req) {
        $responseData = (new PostService(
            new PostRepository(),
            new UserService(new UserRepository()),
        ))->getAll();

        return Response::json($responseData);
    }

    public function update($req) {
        $querySchema = [
            "id_public" => ["string", "required"]
        ];

        $bodySchema = [
            "content" => ["string", "optional", "minLen: 1"],
            "image_name" => ["string", "optional", "maxLen: 255"]
        ];

        try {
            $query = (new Schema())->validate(schema: $querySchema, data: $req['query']);
            $body = (new Schema())->validate(schema: $bodySchema, data: $req['body']);

            $responseData = (new PostService(
                new PostRepository(),
                new UserService(new UserRepository()),
            ))->update($query['id_public'], $body);

            return Response::json($responseData);
        } catch (Exception $execpt) {
            throw new Exception($execpt->getMessage(), $execpt->getCode() === 0 ? Response::HTTP_BAD_REQUEST : $execpt->getCode());
        }
    }
}