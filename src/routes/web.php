<?php

// Route matching functions

function check_request_pattern($routeUri, $requestUri) {
    $pattern = "@^{$routeUri}$@";
    $pattern = str_replace(':id', '([a-zA-Z0-9\-_]+)', $pattern);
    return preg_match($pattern, $requestUri);
}

function find_request_route($routes, $requestUri, $requestMethod) {
    foreach ($routes as $routeUri => $routeMethods) {
        if (check_request_pattern($routeUri, $requestUri)) {
            foreach ($routeMethods as $method) {
                if ($method[0] == $requestMethod) {
                    return $method;
                }
            }
        }
    }
    return false;
}

// Define routes
$routes = [
    // Views
    '/' => [
        ['GET', 'App\Controllers\HomeController', 'index']
    ],
    '/users' => [
        ['GET', 'App\Controllers\UserController', 'index']
    ],
    '/users/create' => [
        ['GET', 'App\Controllers\UserController', 'create']
    ],
    '/users/edit/:id' => [
        ['GET', 'App\Controllers\UserController', 'edit']
    ],
    // End-points
    '/api/users' => [
        ['POST', 'App\Controllers\UserController', 'post'],
        ['GET', 'App\Controllers\UserController', 'get']
    ],
    '/api/users/:id' => [
        ['GET', 'App\Controllers\UserController', 'getById'],
        ['PUT', 'App\Controllers\UserController', 'put'],
        ['DELETE', 'App\Controllers\UserController', 'delete']
    ]
];

// Handle JSON body
$contentType = $_SERVER['CONTENT_TYPE'] ?? $_SERVER['HTTP_CONTENT_TYPE'] ?? '';
if (strpos($contentType, 'application/json') !== false && empty($_POST)) {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    if (is_array($data)) {
        $_POST = array_merge($_POST, $data);
    }
}

// Match request
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestMethod = $_SERVER['REQUEST_METHOD'];
$routeMethod = find_request_route($routes, $uri, $requestMethod);
if ($routeMethod === false) {
    // Redirect any non-match request to root path
    header('Location: /');
    echo '<meta http-equiv="refresh" content="0;url=/">';
    echo '<script>window.location.href = "/";</script>';
} else {
    $method = $routeMethod[0];
    $controller = $routeMethod[1];
    $action = $routeMethod[2];
    $controllerInstance = new $controller();
    $controllerInstance->$action();
}   