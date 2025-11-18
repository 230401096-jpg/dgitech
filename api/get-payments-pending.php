<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/config.php';
session_start();

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

try {
    $stmt = $conn->prepare('SELECT p.id, p.reference, p.order_id, p.user_id, u.name AS user_name, u.email AS user_email, p.amount, p.proof, p.created_at FROM payments p LEFT JOIN users u ON p.user_id = u.id WHERE p.status = ? ORDER BY p.created_at DESC');
    $stmt->execute(['pending']);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $rows]);
} catch (Exception $e) {
    error_log('get-payments-pending error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Server error']);
}

?>
<?php
require_once __DIR__ . '/../includes/config.php';
session_start();

header('Content-Type: application/json');

if (empty($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

try {
    $sql = "SELECT p.id, p.reference, p.order_id, p.user_id, p.amount, p.method, p.proof, p.status, p.created_at, u.name AS user_name, u.email AS user_email
            FROM payments p
            LEFT JOIN users u ON u.id = p.user_id
            WHERE p.status = 'pending'
            ORDER BY p.created_at DESC";

    $stmt = $conn->query($sql);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $rows]);
} catch (Exception $e) {
    http_response_code(500);
    error_log('get-payments-pending error: ' . $e->getMessage());
    echo json_encode(['error' => 'Server error']);
}

?>
