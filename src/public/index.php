<?php

    require_once __DIR__ . '/../bootstrap.php';

    // Determine if the request is for API or web view
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $isApi = (strpos($uri, '/api/') === 0);

    if (!$isApi):
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="/assets/css/global.css">
</head>
<body>
<?php 

        // Load environment variables
        $env = getenv('ENV');
        echo "Environment: " . ($env ?: 'not set');

    endif; 

# Open a connection to the system logger
openlog("Syslog", LOG_ODELAY | LOG_PERROR, LOG_LOCAL0);

# Execute log
syslog(LOG_INFO, "Test - Index.php");
error_log("ErrorLog: Test - Index.php");

// Load routes
require_once __DIR__ . '/../routes/web.php';

if (!$isApi):
?>
<script src="/assets/js/global.js"></script>
</body>
</html>
<?php endif; ?>
