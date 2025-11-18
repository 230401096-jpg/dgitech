<?php
require_once __DIR__ . '/../includes/config.php';
header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User belum login']);
    exit;
}

$uid = $_SESSION['user_id'];
$stmt = $conn->prepare('SELECT * FROM orders WHERE user_id = :uid ORDER BY created_at DESC');
$stmt->execute([':uid' => $uid]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['status' => 'success', 'orders' => $orders]);
