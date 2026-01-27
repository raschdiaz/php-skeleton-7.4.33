<?php
// config/database.php

// Load environment variables
$db_host = getenv('DB_HOST');
$db_name = getenv('DB_NAME');
$db_user = getenv('DB_USER');
$db_pass = getenv('DB_PASS');

// Database configuration
return [
    'db_host' => $db_host,
    'db_name' => $db_name,
    'db_user' => $db_user,
    'db_pass' => $db_pass,
];   