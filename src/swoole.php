<?php

// Force error reporting and logging to stderr for Docker visibility
#date_default_timezone_set('UTC');
#ini_set('display_errors', '1');
#ini_set('display_startup_errors', '1');
#error_reporting(E_ALL);

function console_log($msg) {
    $output = "[" . date('Y-m-d H:i:s') . "] " . $msg . "\n";
    file_put_contents('php://stderr', $output);
    echo $output;
}

console_log("Initializing Swoole script...");

if (!extension_loaded('swoole')) {
    console_log("FATAL ERROR: Swoole extension is not loaded.");
    exit(1);
}

use Swoole\Http\Server;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Process;

try {
    $server = new Server("0.0.0.0", 9501);

    $server->on("Start", function(Server $server)
    {
        console_log("Swoole http server is started at http://0.0.0.0:9501");
    });

    $server->on("Request", function(Request $request, Response $response)
    {
        $response->header("Content-Type", "text/html");
        
        // Capture output from index.php
        ob_start();
        try {
            include __DIR__ . '/index.php';
        } catch (Throwable $e) {
            console_log("Error in index.php: " . $e->getMessage());
            echo $e->getMessage();
        }
        $output = ob_get_clean();
        
        $response->end($output);
    });

    // Watch for file changes and restart the server
    $server->addProcess(new Process(function($process) use ($server) {
        $files = [
            __FILE__ => 'shutdown', // Hard restart for server config
            __DIR__ => 'reload', // Soft reload for app logic
        ];
        
        $last_mtimes = [];
        foreach ($files as $f => $act) {
            $last_mtimes[$f] = file_exists($f) ? filemtime($f) : 0;
        }

        console_log("Watcher process started.");

        while (true) {
            sleep(1);
            clearstatcache();
            foreach ($files as $f => $act) {
                if (!file_exists($f)) continue;
                $mtime = filemtime($f);
                if ($mtime === false) continue;
                
                if (($last_mtimes[$f] ?? 0) === 0) {
                    $last_mtimes[$f] = $mtime;
                    continue;
                }
                if ($mtime > $last_mtimes[$f]) {
                    console_log("File changed: " . basename($f) . " -> $act");
                    $last_mtimes[$f] = $mtime;
                    $server->$act();
                    if ($act === 'shutdown') break 2;
                }
            }
        }
    }));

    console_log("Starting Swoole server...");
    $server->start();
    console_log("Swoole server stopped normally.");
} catch (Throwable $e) {
    console_log("FATAL ERROR: " . $e->getMessage());
    exit(1);
}
