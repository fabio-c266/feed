<?php

use src\core\Routes;

try {
    Routes::post(endpoint: '/users', controller: 'UserController::create');
    Routes::get(endpoint: '/users', controller: 'UserController::get', requireAuth: true);
} catch (Exception $except) {
    die($except->getMessage());
}