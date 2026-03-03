<?php

// Serve static files directly when using PHP's built-in development server
$requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$staticFile  = __DIR__ . $requestPath;
if ($requestPath !== '/' && $requestPath !== '/index.php' && is_file($staticFile)) {
    $ext  = strtolower(pathinfo($staticFile, PATHINFO_EXTENSION));
    $mime = [
        'jpg'  => 'image/jpeg', 'jpeg' => 'image/jpeg',
        'png'  => 'image/png',  'gif'  => 'image/gif',
        'webp' => 'image/webp', 'svg'  => 'image/svg+xml',
        'ico'  => 'image/x-icon',
        'css'  => 'text/css',   'js'   => 'application/javascript',
        'pdf'  => 'application/pdf',
    ];
    header('Content-Type: ' . ($mime[$ext] ?? 'application/octet-stream'));
    header('Access-Control-Allow-Origin: *');
    readfile($staticFile);
    exit;
}

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Load bootstrap
require_once __DIR__ . '/../app/helpers/Bootstrap.php';

// Load environment variables
if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = parse_ini_file(__DIR__ . '/../.env');
    foreach ($dotenv as $key => $value) {
        if (!getenv($key)) {
            putenv("$key=$value");
        }
    }
}

// Autoloader for classes
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../app/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// Get router
$router = require __DIR__ . '/../routes/api.php';

// Get request method and path
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if (empty($path) || $path === '/') {
    $path = '/api';
}

// Dispatch request
try {
    $router->dispatch($method, $path);
} catch (\Exception $e) {
    error('An error occurred: ' . $e->getMessage(), 500);
}
