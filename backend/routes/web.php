<?php

use App\Controllers\PrincipalController;

// Array para almacenar las rutas
$routes = [];

// Función para registrar una ruta GET
function get($route, $action) {
    global $routes;
    $routes['GET'][$route] = $action;
}

// Función para registrar una ruta POST
function post($route, $action) {
    global $routes;
    $routes['POST'][$route] = $action;
}

// Definir la ruta raíz que apunta al método `index` de `PrincipalController`
get('/', [PrincipalController::class, 'index']);
