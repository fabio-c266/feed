<?php

namespace src\Core;

use Exception;
use src\Core\BroswerCore;
use src\Core\JWT;

require './src/routes.php';

class Request
{
    public static function handler($server)
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
                    return throw new Exception('Invalid token.', Response::HTTP_UNAUTHORIZED);
                }

                $server['jwt_data'] = $jwtData;
            }

            $className = $route->controllerName;
            $class = "src\Controllers\\{$className}";
            $classInstance = new $class();

            if ($route->httpMethod === "POST") {
                $body = json_decode(file_get_contents('php://input'), true);
                $server['body'] = $body ?? [];
            }

            $server['query'] = $parsed_url['query'] ?? '';

            echo call_user_func([$classInstance, $route->controllerMethod], $server);
        } catch (Exception $except) {
            $data = [
                "message" => $except->getMessage(),
            ];

            echo Response::json($data, $except->getCode());
        }
    }
}
