<?php

// Load .env early so FRONTEND_URL is available for CORS before the full bootstrap.
$_earlyEnv = __DIR__ . '/.env';
if (file_exists($_earlyEnv)) {
    foreach (parse_ini_file($_earlyEnv) as $_k => $_v) {
        if (!getenv($_k)) putenv("$_k=$_v");
    }
}

// Send CORS headers immediately — before any code that could throw errors.
// FRONTEND_URL may be a comma-separated list for multiple allowed origins.
$requestOrigin   = $_SERVER['HTTP_ORIGIN'] ?? '';
$_frontendUrl    = getenv('FRONTEND_URL') ?: 'http://localhost:5173';
$_allowedOrigins = array_values(array_filter(array_map('trim', explode(',', $_frontendUrl))));

if (preg_match('#^https?://localhost(:\d+)?$#', $requestOrigin)
    || in_array($requestOrigin, $_allowedOrigins, true)) {
    header('Access-Control-Allow-Origin: ' . $requestOrigin);
} else {
    header('Access-Control-Allow-Origin: ' . ($_allowedOrigins[0] ?? 'http://localhost:5173'));
}
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: true');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Detect app root: supports root-level placement (app/ inside backend/),
// same-directory placement (public_html/api/ with app/ alongside), and
// legacy backend/public/ layout (app/ one level up).
if (is_dir(__DIR__ . '/backend/app')) {
    $APP_ROOT = __DIR__ . '/backend';
} elseif (is_dir(__DIR__ . '/app')) {
    $APP_ROOT = __DIR__;
} else {
    $APP_ROOT = __DIR__ . '/..';
}

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
// If not found at __DIR__, also check under public/ subdirectory
// (handles: php -S localhost:8000 index.php run from backend/ while uploads live in backend/public/)
if (!is_file($staticFile)) {
    $publicStatic = __DIR__ . '/public' . $requestPath;
    if (is_file($publicStatic)) $staticFile = $publicStatic;
}
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
    readfile($staticFile);
    exit;
}

header('Content-Type: application/json');

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
