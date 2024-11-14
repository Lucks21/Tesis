<?php

use App\Controllers\PrincipalController;

$routes = [];

function get($route, $action) {
    global $routes;
    $routes['GET'][$route] = $action;
}

function post($route, $action) {
    global $routes;
    $routes['POST'][$route] = $action;
}

get('/', [PrincipalController::class, 'index']);
