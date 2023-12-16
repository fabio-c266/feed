<?php

namespace src\Services;

use Exception;
use src\core\Response;
use src\core\UUID;
use src\helpers\StringHelper;
use src\repositories\ImageRepository;

class ImageService
{
    public function __construct(
        private readonly ImageRepository $imageRepository,
    ) {
    }

    public function upload($file)
    {
        if (!isset($file['name']) || !str_contains($file['name'], '.')) {
            throw new Exception('Formato de arquivo inválido.', Response::HTTP_BAD_REQUEST);
        }

        if (!StringHelper::allowImageType($file['type'])) {
            throw new Exception('A imagem deve ser do formato jpg ou png.', Response::HTTP_BAD_REQUEST);
        }

        [, $extension] = explode('.', $file['name']);
        $newNameUUID = UUID::generate();
        $newImageName = "{$newNameUUID}.{$extension}";

        try {
            $destination_path = getcwd() . DIRECTORY_SEPARATOR . '\uploads\\';
            $target_path = $destination_path . basename($newImageName);
            @move_uploaded_file($file['tmp_name'], $target_path);

            $responseData = [
                'name' => $newImageName
            ];

            return Response::json($responseData, Response::HTTP_CREATED);
        } catch (Exception $except) {
            throw new Exception("Não foi possível criar o usuário.", Response::HTTP_BAD_REQUEST);
        }
    }

    public function get(string $imageName)
    {
        $filePath = "uploads/{$imageName}";

        if (!file_exists($filePath)) {
            throw new Exception("Imagem inválida.", Response::HTTP_BAD_REQUEST);
        }

        return Response::image($filePath);
    }
}
