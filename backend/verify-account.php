<?php
// Verify IT account exists and test login locally

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
    
    // Check if IT user exists
    $stmt = $pdo->prepare("SELECT id, name, email, role, password FROM users WHERE email = ?");
    $stmt->execute(['it@joannasnook.com']);
    $itUser = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($itUser) {
        echo "✓ IT Account Found\n";
        echo "  ID: {$itUser['id']}\n";
        echo "  Name: {$itUser['name']}\n";
        echo "  Email: {$itUser['email']}\n";
        echo "  Role: {$itUser['role']}\n";
        
        // Test password verification
        $testPassword = 'ITAdmin123!';
        $passwordMatch = password_verify($testPassword, $itUser['password']);
        echo "  Password Valid: " . ($passwordMatch ? "YES" : "NO") . "\n";
        
        if (!$passwordMatch) {
            echo "\n⚠ Password mismatch! Hash stored: {$itUser['password']}\n";
        }
    } else {
        echo "✗ IT Account NOT found in database\n";
        echo "\nAll users in database:\n";
        $stmt = $pdo->query("SELECT id, name, email, role FROM users");
        $allUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($allUsers as $u) {
            echo "  - {$u['name']} ({$u['email']}) - Role: {$u['role']}\n";
        }
    }
    
} catch (PDOException $e) {
    echo "✗ Database Error: " . $e->getMessage() . "\n";
}
?>
