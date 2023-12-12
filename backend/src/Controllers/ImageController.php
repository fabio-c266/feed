<?php

namespace src\controllers;

use Exception;
use src\core\Response;
use src\core\UUID;
use src\helpers\ValidationsHelper;
use src\helpers\StringHelper;
use src\repositories\ImageRepository;

class ImageController
{
    public function __construct(private readonly ImageRepository $imageRepository)
    {
    }

    public function upload($req)
    {
        $files = $req['files'];

        if (count($files) < 1) {
            throw new Exception('É necessário por o menos um arquivo.', Response::HTTP_BAD_REQUEST);
        }

        $file = array_values($files)[0];
        if (!isset($file['name']) || !str_contains($file['name'], '.')) {
            throw new Exception('Formato de arquivo inválido.', Response::HTTP_BAD_REQUEST);
        }

        if (!StringHelper::allowImageType($file['type'])) {
            throw new Exception('A imagem deve ser do formato jpg ou png.', Response::HTTP_BAD_REQUEST);
        }

        [, $extension] = explode('.', $file['name']);
        $newNameUUID = UUID::generate();
        $fileNewName = "{$newNameUUID}.{$extension}";

        $data = [
            "id" => UUID::generate(),
            "original_name" => $file['name'],
            "new_name" => $fileNewName
        ];

        try {
            $this->imageRepository->create($data);

            $destination_path = getcwd() . DIRECTORY_SEPARATOR . '\uploads\\';
            $target_path = $destination_path . basename($fileNewName);
            @move_uploaded_file($file['tmp_name'], $target_path);

            $image = $this->imageRepository->findOne($data['id']);

            return Response::json($image, Response::HTTP_CREATED);
        } catch (Exception $execpt) {
            throw new Exception("Não foi possível criar a imagem.", Response::HTTP_BAD_REQUEST);
        }
    }

    public function get($req)
    {
        $queryParaments = $req['query'];
        $queryParamentsSchema = [
            "id" => 'string | required'
        ];

        try {
            ValidationsHelper::schema(schema: $queryParamentsSchema, data: $queryParaments);
        } catch (Exception $execpt) {
            throw new Exception($execpt->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        $id = $queryParaments['id'];
        $image = $this->imageRepository->findOne($id);

        if (!$image) {
            throw new Exception("Imagem inválida.", Response::HTTP_BAD_REQUEST);
        }

        $filePath = "uploads/{$image['new_name']}";

        if (!file_exists($filePath)) {
            throw new Exception("Infelizmente essa imagem foi deletada.", Response::HTTP_BAD_REQUEST);
        }

        return Response::image($filePath);
    }
}
