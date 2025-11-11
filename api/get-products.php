<?php
require_once __DIR__ . '/../includes/config.php';
header('Content-Type: application/json');

$stmt = $conn->prepare("SELECT id, title, price, category FROM products WHERE is_active = 1");
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'status' => 'success',
    'data'   => $products
]);
