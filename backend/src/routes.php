<?php

use src\core\Routes;

try {
    /* AUTH */
    Routes::post(endpoint: '/auth/login', controller: 'AuthController::login');
    
    /* USERS */
    Routes::post(endpoint: '/users', controller: 'UserController::create');
    Routes::get(endpoint: '/users', controller: 'UserController::get', requireAuth: true);
    Routes::put(endpoint: '/users', controller: 'UserController::update', requireAuth: true);

    /* IMAGES */
    Routes::post(endpoint: '/images/upload', controller: 'ImageController::upload', requireAuth: true);
    Routes::get(endpoint: '/images', controller: 'ImageController::get');

    /* POSTS */
    Routes::post(endpoint: '/posts', controller: 'PostController::create', requireAuth: true);
    Routes::get(endpoint: '/posts', controller: 'PostController::get', requireAuth: true);
    Routes::get(endpoint: '/posts/all', controller: 'PostController::getAll', requireAuth: true);
    Routes::put(endpoint: '/posts', controller: 'PostController::update', requireAuth: true);
} catch (Exception $except) {
    die($except->getMessage());
}
