<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/config.php';
session_start();

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$body = json_decode(file_get_contents('php://input'), true);
if (!is_array($body)) {
    echo json_encode(['success' => false, 'error' => 'Invalid payload']);
    exit;
}

$payment_id = isset($body['payment_id']) ? (int)$body['payment_id'] : 0;
$action = isset($body['action']) ? $body['action'] : '';

if ($payment_id <= 0 || !in_array($action, ['approve', 'reject'], true)) {
    echo json_encode(['success' => false, 'error' => 'Invalid parameters']);
    exit;
}

try {
    $stmt = $conn->prepare('SELECT id, status FROM payments WHERE id = ? FOR UPDATE');
    $stmt->execute([$payment_id]);
    $payment = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$payment) {
        echo json_encode(['success' => false, 'error' => 'Payment not found']);
        exit;
    }

    if ($payment['status'] !== 'pending') {
        echo json_encode(['success' => false, 'error' => 'Payment is not pending']);
        exit;
    }

    $newStatus = $action === 'approve' ? 'paid' : 'failed';
    $adminId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;

    $update = $conn->prepare('UPDATE payments SET `status` = ?, verified_by = ?, verified_at = NOW() WHERE id = ?');
    $update->execute([$newStatus, $adminId, $payment_id]);

    echo json_encode(['success' => true, 'status' => $newStatus]);
} catch (Exception $e) {
    error_log('verify_payment error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Server error']);
}

?>
<?php
require_once __DIR__ . '/../includes/config.php';
session_start();

header('Content-Type: application/json');

// Simple admin check
if (empty($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input || empty($input['payment_id']) || empty($input['action'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request']);
    exit;
}

$paymentId = (int)$input['payment_id'];
$action = $input['action']; // expect 'approve' or 'reject'

try {
    // Check payment exists
    $stmt = $conn->prepare('SELECT id, status FROM payments WHERE id = :id');
    $stmt->execute([':id' => $paymentId]);
    $payment = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$payment) {
        http_response_code(404);
        echo json_encode(['error' => 'Payment not found']);
        exit;
    }

    if ($action === 'approve') {
        $newStatus = 'paid';
    } else {
        $newStatus = 'failed';
    }

    $update = $conn->prepare('UPDATE payments SET status = :status, verified_by = :verified_by, verified_at = NOW() WHERE id = :id');
    $update->execute([
        ':status' => $newStatus,
        ':verified_by' => $_SESSION['user_id'],
        ':id' => $paymentId,
    ]);

    echo json_encode(['success' => true, 'payment_id' => $paymentId, 'status' => $newStatus]);
} catch (Exception $e) {
    http_response_code(500);
    error_log('verify_payment error: ' . $e->getMessage());
    echo json_encode(['error' => 'Server error']);
}

?>
