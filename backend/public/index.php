<?php

require_once __DIR__ . '/../vendor/autoload.php'; // Asegúrate de que esta línea esté incluida
require_once __DIR__ . '/../routes/web.php';

// Obtener el método y la URI de la solicitud
$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Buscar y ejecutar la ruta correspondiente
$routeFound = false;
if (isset($routes[$method])) {
    foreach ($routes[$method] as $route => $action) {
        // Convertir rutas con parámetros (e.g., /user/{id})
        $routePattern = preg_replace('/\{[^}]+\}/', '([^/]+)', $route);
        $routePattern = '#^' . $routePattern . '$#';

        if (preg_match($routePattern, $uri, $matches)) {
            array_shift($matches); // Eliminar la coincidencia completa de la ruta
            $controller = new $action[0]();
            call_user_func_array([$controller, $action[1]], $matches);
            $routeFound = true;
            break;
        }
    }
}

if (!$routeFound) {
    http_response_code(404);
    echo "Página no encontrada";
}
