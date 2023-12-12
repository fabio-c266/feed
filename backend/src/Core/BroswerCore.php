<?php

namespace src\core;

class BroswerCore
{
    public static function resolve($server): void
    {
        if ($server['REQUEST_METHOD'] === 'OPTIONS') {
            header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
            header('Access-Control-Allow-Headers: X-CSRF-Token, X-Requested-With, Accept, Accept-Version, Content-Length, Content-MD5, Content-Type, Date, X-Api-Version, Authorization');
            header('Access-Control-Allow-Credentials: true');
            header('HTTP/1.1 200 OK');
            exit();
        }
    }
}
