<?php

// Detect app root: supports local dev (backend/public/ with app/ one level up)
// and Hostinger layout (public_html/api/ with app/ inside the same folder).
$APP_ROOT = is_dir(__DIR__ . '/app') ? __DIR__ : __DIR__ . '/..';

// Load environment variables early so FRONTEND_URL is available for CORS headers.
if (file_exists($APP_ROOT . '/.env')) {
    $dotenv = parse_ini_file($APP_ROOT . '/.env');
    foreach ($dotenv as $key => $value) {
        if (!getenv($key)) {
            putenv("$key=$value");
        }
    }
}

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
    header('Access-Control-Allow-Origin: ' . (getenv('FRONTEND_URL') ?: '*'));
    readfile($staticFile);
    exit;
}

$allowedOrigin = getenv('FRONTEND_URL') ?: '*';
header('Access-Control-Allow-Origin: ' . $allowedOrigin);
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Load bootstrap
require_once $APP_ROOT . '/app/helpers/Bootstrap.php';

// Autoloader for classes
spl_autoload_register(function ($class) use ($APP_ROOT) {
    $prefix = 'App\\';
    $base_dir = $APP_ROOT . '/app/';

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
$router = require $APP_ROOT . '/routes/api.php';

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
