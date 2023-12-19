<?php

namespace src\controllers;

use Exception;
use src\core\Response;
use src\core\Schema;
use src\repositories\UserRepository;
use src\services\UserService;

class UserController
{
    public function create($req)
    {
        $bodySchema = [
            "username" => ['string', 'required', 'minLen:6', 'maxLen:16'],
            "email" => ['string', 'required', 'email'],
            'password' => ['string', 'required', 'minLen:6', 'maxLen:20'],
        ];

        try {
            $body = (new Schema())->validate(schema: $bodySchema, data: $req['body']);
            $responseData = (new UserService(new UserRepository()))->create($body);

            return Response::json($responseData, Response::HTTP_CREATED);
        } catch (Exception $execpt) {
            throw new Exception($execpt->getMessage(), $execpt->getCode() === 0 ? Response::HTTP_BAD_REQUEST : $execpt->getCode());
        }
    }

    public function get($req)
    {
        $jwt = $req['jwt_data'];
        $jwtData = $jwt->data;

        try {
            $responseData = (new UserService(new UserRepository()))->get($jwtData->id_public);
            return Response::json($responseData);
        } catch (Exception $execpt) {
            throw new Exception($execpt->getMessage(), $execpt->getCode() === 0 ? Response::HTTP_BAD_REQUEST : $execpt->getCode());
        }
    }

    public function update($req)
    {
        $jwt = $req['jwt_data'];
        $jwtData = $jwt->data;

        $bodySchema = [
            "username" => ['string', 'optional', 'minLen:6', 'maxLen:16'],
            "avatar_name" => ['string', 'optional', 'maxLen:255']
        ];

        try {
            $body = (new Schema())->validate(schema: $bodySchema, data: $req['body']);
            $responseData = (new UserService(new UserRepository()))->update($jwtData->id_public, $body);

            return Response::json($responseData);
        } catch (Exception $execpt) {
            throw new Exception($execpt->getMessage(), $execpt->getCode() === 0 ? Response::HTTP_BAD_REQUEST : $execpt->getCode());
        }
    }
}
