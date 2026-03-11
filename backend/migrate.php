<?php
// Run all migrations in database/migrations/
// Usage (CLI):  php migrate.php
// Usage (web):  https://yoursite.com/api/migrate.php  (protected by MIGRATE_TOKEN)

// --- Auth guard for web requests ---
if (php_sapi_name() !== 'cli') {
    $expectedToken = getenv('MIGRATE_TOKEN') ?: (isset($_ENV['MIGRATE_TOKEN']) ? $_ENV['MIGRATE_TOKEN'] : '');
    $providedToken = $_GET['token'] ?? '';
    if (!$expectedToken || !hash_equals($expectedToken, $providedToken)) {
        http_response_code(403);
        exit("Forbidden: provide ?token=<MIGRATE_TOKEN>\n");
    }
}

// --- Load .env credentials ---
$envFile = file_exists(__DIR__ . '/.env') ? __DIR__ . '/.env' : __DIR__ . '/../.env';
if (file_exists($envFile)) {
    foreach (parse_ini_file($envFile) as $k => $v) {
        $_ENV[$k] = $v;
    }
}

$host = $_ENV['DB_HOST']     ?? 'localhost';
$db   = $_ENV['DB_DATABASE'] ?? 'joannasnook';
$user = $_ENV['DB_USERNAME'] ?? 'root';
$pass = $_ENV['DB_PASSWORD'] ?? '';
$port = $_ENV['DB_PORT']     ?? 3306;

// --- Locate migrations directory ---
$migrationsDir = __DIR__ . '/../database/migrations';
if (!is_dir($migrationsDir)) {
    // Hostinger layout: database/ is alongside api/
    $migrationsDir = __DIR__ . '/database/migrations';
}

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $files = glob($migrationsDir . '/*.sql');
    sort($files);

    $results = [];
    foreach ($files as $file) {
        $name = basename($file);
        $sql  = file_get_contents($file);
        try {
            $pdo->exec($sql);
            $results[] = "✓ $name";
        } catch (PDOException $e) {
            $results[] = "✗ $name — " . $e->getMessage();
        }
    }

    if (php_sapi_name() === 'cli') {
        echo implode("\n", $results) . "\n";
    } else {
        header('Content-Type: text/plain');
        echo implode("\n", $results) . "\n";
    }

} catch (PDOException $e) {
    $msg = "✗ Connection failed: " . $e->getMessage();
    if (php_sapi_name() !== 'cli') { header('Content-Type: text/plain'); }
    exit($msg . "\n");
}
?>
