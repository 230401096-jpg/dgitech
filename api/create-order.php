<?php
require_once __DIR__ . '/../includes/config.php';
header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User belum login']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input || empty($input['items']) || !is_array($input['items'])) {
    echo json_encode(['status' => 'error', 'message' => 'Data item kosong atau tidak valid']);
    exit;
}

$uid = $_SESSION['user_id'];
$shipping = $input['shipping_method'] ?? null;

try {
    $conn->beginTransaction();
    $total = 0;
    foreach ($input['items'] as $it) {
        $total += (float)($it['price'] ?? 0) * (int)($it['qty'] ?? 1);
    }

    $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, shipping_method, status, created_at) VALUES (:uid, :total, :ship, 'pending', NOW())");
    $stmt->execute([':uid' => $uid, ':total' => $total, ':ship' => $shipping]);
    $order_id = $conn->lastInsertId();

    $stmtItem = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (:oid, :pid, :qty, :price)");
    foreach ($input['items'] as $it) {
        $pid = (int)($it['product_id'] ?? 0);
        $qty = (int)($it['qty'] ?? 1);
        $price = (float)($it['price'] ?? 0);
        $stmtItem->execute([':oid' => $order_id, ':pid' => $pid, ':qty' => $qty, ':price' => $price]);
    }

    $conn->commit();
    echo json_encode(['status' => 'success', 'order_id' => $order_id]);
} catch (Exception $e) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    error_log('create-order error: ' . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Gagal membuat order']);
}
