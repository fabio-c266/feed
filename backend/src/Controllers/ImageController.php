<?php

namespace src\controllers;

use Exception;
use src\core\Response;
use src\core\UUID;
use src\helpers\ValidationsHelper;
use src\helpers\StringHelper;
use src\repositories\ImageRepository;
use src\Services\ImageService;

class ImageController
{
    public function upload($req)
    {
        $files = $req['files'];

        if (count($files) < 1) {
            throw new Exception('É necessário por o menos um arquivo.', Response::HTTP_BAD_REQUEST);
        }

        try {
            return (new ImageService(new ImageRepository()))->upload(array_values($files)[0]);
        } catch (Exception $execpt) {
            throw new Exception($execpt->getMessage(), $execpt->getCode() === 0 ? Response::HTTP_BAD_REQUEST : $execpt->getCode());
        }
    }

    public function get($req)
    {
        $queryParaments = $req['query'];
        $queryParamentsSchema = [
            "name" => 'string | required'
        ];

        try {
            ValidationsHelper::schema(schema: $queryParamentsSchema, data: $queryParaments);
            return (new ImageService(new ImageRepository()))->get($queryParaments['name']);
        } catch (Exception $execpt) {
            throw new Exception($execpt->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function delete($req)
    {
    }
}
