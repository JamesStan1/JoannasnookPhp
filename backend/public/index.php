<?php

// Load .env early so FRONTEND_URL is available for CORS before the full bootstrap.
// On Hostinger (deployed as api/index.php), .env lives alongside this file.
// Locally (backend/public/index.php), .env lives one level up in backend/.
$_earlyEnv = file_exists(__DIR__ . '/.env') ? __DIR__ . '/.env' : __DIR__ . '/../.env';
if (file_exists($_earlyEnv)) {
    foreach (parse_ini_file($_earlyEnv) as $_k => $_v) {
        $_ENV[$_k] = $_v;
        if (!getenv($_k)) @putenv("$_k=$_v");
    }
}

// Send CORS headers immediately — before any code that could throw errors.
// FRONTEND_URL may be a comma-separated list for multiple allowed origins.
$requestOrigin   = $_SERVER['HTTP_ORIGIN'] ?? '';
$_frontendUrl    = $_ENV['FRONTEND_URL'] ?? getenv('FRONTEND_URL') ?: 'http://localhost:5173';
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

// Detect app root:
//  - Hostinger deployment: app/ lives alongside this file (public_html/api/app/)
//  - Local dev:            app/ lives one level up        (backend/app/)
if (is_dir(__DIR__ . '/app')) {
    $APP_ROOT = __DIR__;
} else {
    $APP_ROOT = __DIR__ . '/..';
}

// Define uploads base path once, reliably from APP_ROOT.
// Hostinger: APP_ROOT = public_html/api  → uploads at public_html/api/uploads/
// Local dev:  APP_ROOT = backend/        → uploads at backend/public/uploads/
if (!defined('UPLOADS_BASE_PATH')) {
    $_uploadsCandidate = $APP_ROOT . '/uploads/';
    $_uploadsFallback  = $APP_ROOT . '/public/uploads/';
    define('UPLOADS_BASE_PATH', is_dir($_uploadsCandidate) ? $_uploadsCandidate : $_uploadsFallback);
}

// Load environment variables (second pass, in case early load used parent .env).
if (file_exists($APP_ROOT . '/.env')) {
    $dotenv = parse_ini_file($APP_ROOT . '/.env');
    foreach ($dotenv as $key => $value) {
        $_ENV[$key] = $value;
        if (!getenv($key)) @putenv("$key=$value");
    }
}

// Serve static files directly when using PHP's built-in development server.
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
    // On Linux (e.g. Hostinger) paths are case-sensitive.
    // Namespace directories use TitleCase (Controllers, Models…) but the
    // actual folders on disk are lowercase. Keep the class filename as-is.
    $parts     = explode('\\', $relative_class);
    $className = array_pop($parts);
    $dirPath   = strtolower(implode('/', $parts));
    $file      = $base_dir . ($dirPath ? $dirPath . '/' : '') . $className . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// Get router
$router = require $APP_ROOT . '/routes/api.php';

// Get request method and path.
// On shared hosting (e.g. Hostinger), Apache's mod_rewrite updates REQUEST_URI
// to the rewritten script path (/api/index.php) when the parent public_html/.htaccess
// routes /api/* to api/index.php internally. REDIRECT_URL is set by Apache to
// the original client-requested URI before the rewrite, so prefer it.
$method  = $_SERVER['REQUEST_METHOD'];
$rawUri  = $_SERVER['REDIRECT_URL'] ?? $_SERVER['REQUEST_URI'] ?? '/';
$path    = parse_url($rawUri, PHP_URL_PATH);

if (empty($path) || $path === '/') {
    $path = '/api';
}

// Dispatch request
try {
    $router->dispatch($method, $path);
} catch (\Exception $e) {
    error('An error occurred: ' . $e->getMessage(), 500);
}
