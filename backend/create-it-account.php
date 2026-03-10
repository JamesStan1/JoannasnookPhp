<?php
// Add IT role to database and create IT account

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
    
    // 1. Update users table to add 'it' role to enum
    echo "1. Updating users table role enum to include 'it'...\n";
    $pdo->exec("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'manager', 'front_desk', 'housekeeping', 'chef', 'accountant', 'it', 'security', 'guest') NOT NULL");
    echo "   ✓ Users table updated\n";
    
    // 2. Check if IT account already exists
    echo "2. Checking for existing IT account...\n";
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute(['it@joannasnook.com']);
    
    if ($stmt->fetch()) {
        echo "   ℹ IT account already exists (it@joannasnook.com)\n";
    } else {
        // 3. Create IT account
        echo "3. Creating IT account...\n";
        $hashedPassword = password_hash('ITAdmin123!', PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("
            INSERT INTO users (name, email, password, phone, role, active, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");
        $stmt->execute([
            'IT Staff',
            'it@joannasnook.com',
            $hashedPassword,
            '+63-900-000-0000',
            'it',
            1
        ]);
        echo "   ✓ IT account created successfully\n";
        echo "\n   Credentials:\n";
        echo "   Email: it@joannasnook.com\n";
        echo "   Password: ITAdmin123!\n";
    }
    
    echo "\n✓ Migration completed successfully\n";
    
} catch (PDOException $e) {
    echo "✗ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
?>
