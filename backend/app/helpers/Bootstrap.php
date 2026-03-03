<?php

// Composer autoloader (optional - project has custom autoloader)
// require_once __DIR__ . '/../../vendor/autoload.php';

// Load environment variables
if (file_exists(__DIR__ . '/../../.env')) {
    $dotenv = parse_ini_file(__DIR__ . '/../../.env');
    foreach ($dotenv as $key => $value) {
        putenv("$key=$value");
    }
}

// Database connection
class Database {
    private static $connection = null;

    public static function connect() {
        if (self::$connection === null) {
            $config = require __DIR__ . '/../../config/database.php';
            $conn = $config['connections']['mysql'];

            try {
                self::$connection = new PDO(
                    "mysql:host={$conn['host']};port={$conn['port']};dbname={$conn['database']};charset={$conn['charset']}",
                    $conn['username'],
                    $conn['password'],
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    ]
                );
            } catch (PDOException $e) {
                die('Database connection failed: ' . $e->getMessage());
            }
        }

        return self::$connection;
    }
}

// Load configuration
function config($key, $default = null) {
    $parts = explode('.', $key);
    $file = __DIR__ . '/../../config/' . array_shift($parts) . '.php';

    if (!file_exists($file)) {
        return $default;
    }

    $config = require $file;
    foreach ($parts as $part) {
        if (isset($config[$part])) {
            $config = $config[$part];
        } else {
            return $default;
        }
    }

    return $config;
}

// Helper functions
function response($data, $status = 200, $message = null) {
    http_response_code($status);
    header('Content-Type: application/json');

    $response = [
        'success' => $status >= 200 && $status < 300,
        'data' => $data,
    ];

    if ($message) {
        $response['message'] = $message;
    }

    echo json_encode($response);
    exit;
}

function error($message, $status = 400) {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => $message,
    ]);
    exit;
}

return [
    'database' => Database::class,
];
