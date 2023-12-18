<?php

namespace src\controllers;

use Exception;
use src\core\Response;
use src\core\Schema;
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
            $responseData = (new ImageService())->upload(array_values($files)[0]);
            return Response::json($responseData, Response::HTTP_CREATED);
        } catch (Exception $execpt) {
            throw new Exception($execpt->getMessage(), $execpt->getCode() === 0 ? Response::HTTP_BAD_REQUEST : $execpt->getCode());
        }
    }

    public function get($req)
    {
        $queryParamentsSchema = [
            "name" => ['string', 'required']
        ];

        try {
            $data = (new Schema())->validate(schema: $queryParamentsSchema, data: $req['query']);
            $imagePath = (new ImageService())->getImagePath($data['name']);

            return Response::image($imagePath);
        } catch (Exception $execpt) {
            throw new Exception($execpt->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
