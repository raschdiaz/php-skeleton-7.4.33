<?php

// Route matching functions

function find_request_route($routes, $requestUri, $requestMethod) {
    foreach ($routes as $routeUri => $routeMethods) {
        // Convert route URI to a regex pattern
        $pattern = "@^" . preg_replace('/:\w+/', '([a-zA-Z0-9\-_]+)', $routeUri) . "$@";

        // Check if the request URI matches the pattern
        if (preg_match($pattern, $requestUri, $matches)) {
            // Remove the full match from the beginning of the array
            array_shift($matches);
            $params = $matches;

            foreach ($routeMethods as $method) {
                if ($method[0] == $requestMethod) {
                    return [$method, $params];
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
$routeInfo = find_request_route($routes, $uri, $requestMethod);
if ($routeInfo === false) {
    // Redirect any non-match request to root path
    header('Location: /');
    echo '<meta http-equiv="refresh" content="0;url=/">';
    echo '<script>window.location.href = "/";</script>';
} else {
    list($routeMethod, $params) = $routeInfo;
    $method = $routeMethod[0];
    $controller = $routeMethod[1];
    $action = $routeMethod[2];
    $controllerInstance = new $controller();
    // Log request api details
    if (strpos($uri, '/api/') === 0) {
        syslog(LOG_INFO, "Request API URL: " . $method . " " . $uri . " -> " . $controller . "@" . $action . "() with POST data: " . json_encode($_POST));
    }
    // Log request web details
    //syslog(LOG_INFO, "Request URL: " . $method . " " . $uri . " -> " . $controller . "@" . $action . "() with POST data: " . json_encode($_POST));
    // Call the controller action with the extracted parameters
    call_user_func_array([$controllerInstance, $action], $params);
}   