<?php

use src\core\Routes;

try {
    Routes::post(endpoint: '/auth/login', controller: 'AuthController::login');
    Routes::post(endpoint: '/users', controller: 'UserController::create');
    Routes::get(endpoint: '/users', controller: 'UserController::get', requireAuth: true);
    Routes::put(endpoint: '/users', controller: 'UserController::update', requireAuth: true);

    Routes::post(endpoint: '/images/upload', controller: 'ImageController::upload', requireAuth: true);
    Routes::get(endpoint: '/images', controller: 'ImageController::get');
} catch (Exception $except) {
    die($except->getMessage());
}
