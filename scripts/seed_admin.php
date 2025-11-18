<?php
// scripts/seed_admin.php
// Usage: php scripts/seed_admin.php --email=admin@dgitech.local --password=YourPass

require_once __DIR__ . '/../includes/config.php';

$opts = getopt('', ['email:', 'password:']);
$email = $opts['email'] ?? null;
$password = $opts['password'] ?? null;

if (!$email || !$password) {
    echo "Usage: php scripts/seed_admin.php --email=admin@dgitech.local --password=YourPass\n";
    exit(1);
}

$hash = password_hash($password, PASSWORD_DEFAULT);

// insert or update
$stmt = $conn->prepare('SELECT id FROM users WHERE email = :email');
$stmt->execute([':email' => $email]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row) {
    $stmt2 = $conn->prepare('UPDATE users SET password = :pass, role = "admin", is_active = 1 WHERE id = :id');
    $stmt2->execute([':pass' => $hash, ':id' => $row['id']]);
    echo "Updated admin user: {$email}\n";
} else {
    $stmt2 = $conn->prepare('INSERT INTO users (name, email, password, role, is_active, created_at) VALUES (:name, :email, :pass, "admin", 1, NOW())');
    $stmt2->execute([':name' => 'Admin DGITECH', ':email' => $email, ':pass' => $hash]);
    echo "Created admin user: {$email}\n";
}
