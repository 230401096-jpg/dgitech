<?php
require_once __DIR__ . '/../includes/config.php';
header('Content-Type: application/json');

$stmt = $conn->prepare('SELECT s.id, s.title, s.description, s.price, u.name as mitra_name FROM services s LEFT JOIN users u ON s.mitra_id = u.id WHERE s.is_active = 1 ORDER BY s.id DESC');
$stmt->execute();
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['status' => 'success', 'services' => $services]);
