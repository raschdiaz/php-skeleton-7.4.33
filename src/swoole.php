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
        // Polyfill globals for compatibility
        $_SERVER['REQUEST_URI'] = $request->server['request_uri'];
        if (isset($request->server['query_string'])) {
            $_SERVER['REQUEST_URI'] .= '?' . $request->server['query_string'];
        }
        $_SERVER['REQUEST_METHOD'] = $request->server['request_method'];
        $_GET = $request->get ?? [];
        $_POST = $request->post ?? [];

        // Handle JSON body
        if (isset($request->header['content-type']) && strpos($request->header['content-type'], 'application/json') !== false) {
            $_SERVER['CONTENT_TYPE'] = $request->header['content-type'];
            $input = $request->rawContent();
            $data = json_decode($input, true);
            if (is_array($data)) {
                $_POST = array_merge($_POST, $data);
            }
        }

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
        $scanDir = __DIR__;
        
        $getPhpFiles = function($dir) {
            $files = [];
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS)
            );
            foreach ($iterator as $file) {
                if ($file->isFile() && $file->getExtension() === 'php') {
                    $files[] = $file->getPathname();
                }
            }
            return $files;
        };

        $last_mtimes = [];
        foreach ($getPhpFiles($scanDir) as $file) {
            $last_mtimes[$file] = filemtime($file);
        }

        console_log("Watcher process started. Monitoring: $scanDir");

        while (true) {
            sleep(1);
            clearstatcache();
            
            $currentFiles = $getPhpFiles($scanDir);
            
            foreach ($currentFiles as $file) {
                $mtime = filemtime($file);
                
                if (!isset($last_mtimes[$file])) {
                    $last_mtimes[$file] = $mtime;
                    console_log("New file detected: " . basename($file));
                    $server->reload();
                    continue;
                }

                if ($mtime > $last_mtimes[$file]) {
                    $last_mtimes[$file] = $mtime;
                    if ($file === __FILE__) {
                        console_log("Config changed: " . basename($file) . " -> shutdown");
                        $server->shutdown();
                        break 2;
                    } else {
                        console_log("File changed: " . basename($file) . " -> reload");
                        $server->reload();
                    }
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
