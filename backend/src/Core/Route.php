<?php

namespace src\core;

class Route
{
    public function __construct(
        public readonly string $httpMethod,
        public readonly string $endpoint,
        public readonly string $controllerName,
        public readonly string $controllerMethod,
        public readonly string $requireAuth
    ) {
    }
}
