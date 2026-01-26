<?php
require_once __DIR__ . '/../bootstrap.php';

// Load routes
require_once __DIR__ . '/../routes/web.php';

# Open a connection to the system logger
openlog("Syslog", LOG_ODELAY | LOG_PERROR, LOG_LOCAL0);

# Execute log
syslog(LOG_INFO, "Test - Index.php");
error_log("ErrorLog: Test - Index.php");

// Load environment variables
$env = getenv('ENV');

// Example route
$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// Simple routing logic
/*if ($uri === '/' && $method === 'GET') {
    echo '<h1>Welcome to Vanilla PHP App</h1>';
} else {
    http_response_code(404);
    echo '<h1>404 - Not Found</h1>';
}*/