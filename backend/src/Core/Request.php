<?php

namespace src\core;

use Exception;
use src\core\BroswerCore;
use src\core\JWT;
use src\helpers\StringHelper;

require './src/routes.php';

class Request
{
    public static function handler($server): void
    {
        BroswerCore::resolve($server);

        $uri = $server['REQUEST_URI'];
        $httpMethod = $server['REQUEST_METHOD'];

        if ($uri !== '/' && substr($uri, -1) === '/') {
            $uri = rtrim($uri, '/');
        }

        $uriData = parse_url($uri);
        $routeUri = $uriData['path'];
        $route = Routes::get_route($httpMethod, $routeUri);

        if (!$route) {
            echo Response::json(["messsage" => "Router not found"], Response::HTTP_NOT_FOUND);
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

            $server['files'] = isset($_FILES) ? $_FILES : [];

            if ($route->httpMethod === "POST" || $route->httpMethod === 'PUT') {
                $body = json_decode(file_get_contents('php://input'), true);
                $server['body'] = $body ?? [];
            }
            
            $server['query'] = StringHelper::getQueryParams($uriData['query'] ?? '');

            echo File::executeClass(fileName: $route->controllerName, classMethod: $route->controllerMethod, methodParams: [$server]);
        } catch (Exception $except) {
            echo Response::json(["message" => $except->getMessage()], $except->getCode());
        }
    }
}
