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
            return (new UserService(new UserRepository()))->create($body);
        } catch (Exception $execpt) {
            throw new Exception($execpt->getMessage(), $execpt->getCode() === 0 ? Response::HTTP_BAD_REQUEST : $execpt->getCode());
        }
    }

    public function get($req)
    {
        $jwt = $req['jwt_data'];
        $jwtData = $jwt->data;

        try {
            return (new UserService(new UserRepository()))->get($jwtData->id_public);
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
            "image" => 'string | nullable | maxLen:255'
        ];

        try {
            ValidationsHelper::schema(schema: $bodySchema, data: $body);
            return (new UserService(new UserRepository()))->update($jwtData->id_public, $body);
        } catch (Exception $execpt) {
            throw new Exception($execpt->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
