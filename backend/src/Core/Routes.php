<?php

namespace src\Core;

use src\Core\Route;

class Routes
{
    private static array $routes = [];

    public static function get($endpoint, $controller, $requireAuth = false)
    {
        return self::save_router('GET', $endpoint, $controller, $requireAuth);
    }

    public static function post($endpoint, $controller, $requireAuth = false)
    {
        return self::save_router('POST', $endpoint, $controller, $requireAuth);
    }

    public static function put($endpoint, $controller, $requireAuth = false)
    {
        return self::save_router('PUT', $endpoint, $controller, $requireAuth);
    }

    public static function delete($endpoint, $controller, $requireAuth = false)
    {
        return self::save_router('DELETE', $endpoint, $controller, $requireAuth);
    }

    public static function get_route(string $httpMethod, $endpoint)
    {
        $foundRoute = null;

        foreach (self::$routes as $currentRoute) {
            if ($currentRoute->httpMethod === $httpMethod && $currentRoute->endpoint === $endpoint) {
                $foundRoute = $currentRoute;
                break;
            }
        }

        return $foundRoute;
    }

    private static function save_router($httpMethod, $endpoint, $controller, bool $requireAuth)
    {
        if (array_keys(self::$routes, $endpoint)) {
            return 'Router with this endpoint is already registered.';
        }

        if (empty($controller) || !str_contains($controller, '::')) {
            return 'Invalid controller format';
        }

        $controllerSplited = explode('::', $controller);
        $controllerName = ucfirst($controllerSplited[0]);
        $controllerMethod = $controllerSplited[1];

        $controllerFile = "src/Controllers/{$controllerName}.php";

        if (!file_exists($controllerFile)) {
            return "Invalid controller";
        }

        $class = "\src\Controllers\\{$controllerName}";

        if (!class_exists($class)) {
            return "This class not exists in file {$controllerName}";
        }

        $classInstance = new $class();

        if (!method_exists($classInstance, $controllerMethod)) {
            return "This method not exists in class.";
        }

        $route = new Route($httpMethod, $endpoint, $controllerName, $controllerMethod, $requireAuth);
        array_push(self::$routes, $route);
    }

    public static function get_all_routes()
    {
        return self::$routes;
    }
}