<?php

require_once __DIR__ . '/../vendor/autoload.php'; 
require_once __DIR__ . '/../routes/web.php';

$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$routeFound = false;
if (isset($routes[$method])) {
    foreach ($routes[$method] as $route => $action) {

        $routePattern = preg_replace('/\{[^}]+\}/', '([^/]+)', $route);
        $routePattern = '#^' . $routePattern . '$#';

        if (preg_match($routePattern, $uri, $matches)) {
            array_shift($matches); 
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
