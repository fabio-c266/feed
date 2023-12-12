<?php

namespace src\core;

use Exception;
use src\core\BroswerCore;
use src\core\JWT;

require './src/routes.php';

class Request
{
    public static function handler($server): void
    {
        BroswerCore::resolve($server);

        $endpoint = $server['REQUEST_URI'];
        $httpMethod = $server['REQUEST_METHOD'];

        if ($endpoint[strlen($endpoint) - 1] === '/') {
            $endpoint = substr($endpoint, 0, strlen($endpoint) - 1);
        }

        $parsed_url = parse_url($endpoint);
        $endpoint = $parsed_url['path'];

        $route = Routes::get_route($httpMethod, $endpoint);

        if (!$route) {
            $data = [
                "messsage" => "Router not found"
            ];

            echo Response::json($data, Response::HTTP_NOT_FOUND);
            return;
        }

        try {
            if ($route->requireAuth) {
                $header = getallheaders();

                if (!isset($header['Authorization'])) {
                    throw new Exception("Is need Bearer token.", Response::HTTP_UNAUTHORIZED);
                }

                $token = explode(' ', $header['Authorization'])[1];
                $jwtData = JWT::get_data($token);

                if (!$jwtData) {
                    throw new Exception('Invalid token.', Response::HTTP_UNAUTHORIZED);
                }

                $server['jwt_data'] = $jwtData;
            }

            if ($route->httpMethod === "POST") {
                $body = json_decode(file_get_contents('php://input'), true);
                $server['body'] = $body ?? [];
            }

            $server['query'] = $parsed_url['query'] ?? '';

            echo File::executeClass(fileName: $route->controllerName, classMethod: $route->controllerMethod, methodParams: [$server]);
        } catch (Exception $except) {
            $data = [
                "message" => $except->getMessage(),
            ];

            echo Response::json($data, $except->getCode());
        }
    }
}
