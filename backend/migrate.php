<?php
// Run migrations
$host = 'localhost';
$db = 'joannasnook';
$user = 'root';
$pass = '';
$port = 3306;

try {
    $pdo = new PDO("mysql:host=$host;port=$port;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Select database
    $pdo->exec("USE $db");
    
    // Read and execute migration
    $sql = file_get_contents(__DIR__ . '/../database/migrations/forgot_password_requests.sql');
    $pdo->exec($sql);
    
    echo "✓ Migration executed successfully: forgot_password_requests table created\n";
    
} catch (PDOException $e) {
    echo "✗ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
?>
