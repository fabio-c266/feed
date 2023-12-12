<?php

namespace src\core;

use Exception;
use ReflectionClass;
use src\core\Route;

class Routes
{
    private static array $routes = [];

    public static function get($endpoint, $controller, $requireAuth = false): void
    {
        self::save_router('GET', $endpoint, $controller, $requireAuth);
    }

    public static function post($endpoint, $controller, $requireAuth = false): void
    {
        self::save_router('POST', $endpoint, $controller, $requireAuth);
    }

    public static function put($endpoint, $controller, $requireAuth = false): void
    {
        self::save_router('PUT', $endpoint, $controller, $requireAuth);
    }

    public static function delete($endpoint, $controller, $requireAuth = false): void
    {
        self::save_router('DELETE', $endpoint, $controller, $requireAuth);
    }

    public static function get_route(string $httpMethod, $endpoint): Route | null
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

    private static function save_router(string $httpMethod, string $endpoint, string $controller, bool $requireAuth): void
    {
        if (empty($controller) || !str_contains($controller, '::')) {
            throw new Exception("Invalid controller format. Use: <controllerName>::<classMethod>");
        }

        $controllerSplited = explode('::', $controller);
        $controllerName = ucfirst($controllerSplited[0]);
        $controllerMethod = $controllerSplited[1];

        $controllerFile = "src/Controllers/{$controllerName}.php";

        if (!file_exists($controllerFile)) {
            throw new Exception("Not found controller file {$controllerName}");
        }

        $class = "\src\Controllers\\{$controllerName}";

        if (!class_exists($class)) {
            throw new Exception("The class {$controllerName} not exists in file {$controllerName}.php");
        }

        $classReflection = new ReflectionClass($class);
        $classConstructor = $classReflection->getConstructor();
        $constructArgs = File::resolveClassConstructorDependencies($classConstructor);

        $classInstance = new $class(...$constructArgs);

        if (!method_exists($classInstance, $controllerMethod)) {
            throw new Exception("The method {$controllerMethod} not exists in class {$controllerName}.");
        }

        $route = new Route($httpMethod, $endpoint, $controllerName, $controllerMethod, $requireAuth);
        array_push(self::$routes, $route);
    }

    public static function get_all_routes()
    {
        return self::$routes;
    }
}
