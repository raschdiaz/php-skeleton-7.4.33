<?php
// Define base path
define('BASE_PATH', __DIR__);

// Load environment variables
//require_once BASE_PATH . '/vendor/vlucas/phpdotenv/src/Dotenv.php';
//$dotenv = new \Dotenv\Dotenv(BASE_PATH);
//$dotenv->load();
//$env = getenv();

// Manual PSR-4 Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'App\\'; // Namespace prefix
    $base_dir = BASE_PATH . '/app/';

    if (strpos($class, $prefix) !== 0) {
        return;
    }

    $relative_class = substr($class, strlen($prefix));
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});