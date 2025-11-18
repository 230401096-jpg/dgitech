<?php
$page_title = "Pembayaran - DGITECH";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
if (!isset($_GET['order_id'])) {
    echo "<p>Order tidak ditemukan.</p>";
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$order_id = intval($_GET['order_id']);
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = :oid AND user_id = :uid");
$stmt->execute([':oid' => $order_id, ':uid' => $_SESSION['user_id']]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$order) {
    echo "<p>Order tidak valid.</p>";
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

if (isset($_POST['submit_payment'])) {
    // Simpan data pembayaran dan unggah bukti (opsional)
    $method = $_POST['method'] ?? 'transfer';
    $ref = uniqid('PAY');

    // handle file upload jika ada
    $uploadFilename = null;
    if (!empty($_FILES['proof']['name'])) {
        $uploadDir = __DIR__ . '/uploads/payments';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $ext = pathinfo($_FILES['proof']['name'], PATHINFO_EXTENSION);
        $safe = $ref . '_' . time() . '.' . $ext;
        $target = $uploadDir . '/' . $safe;
        if (move_uploaded_file($_FILES['proof']['tmp_name'], $target)) {
            $uploadFilename = 'uploads/payments/' . $safe; // path relative web root
        }
    }

    $stmtPay = $conn->prepare("INSERT INTO payments (reference, order_id, user_id, amount, method, status, created_at) VALUES (:ref, :oid, :uid, :amt, :method, 'pending', NOW())");
    $stmtPay->execute([
        ':ref' => $ref,
        ':oid' => $order_id,
        ':uid' => $_SESSION['user_id'],
        ':amt' => $order['total_amount'],
        ':method' => $method
    ]);
    $payment_id = $conn->lastInsertId();

    if ($uploadFilename) {
        $stmtUpd = $conn->prepare('UPDATE payments SET proof = :proof WHERE id = :id');
        $stmtUpd->execute([':proof' => $uploadFilename, ':id' => $payment_id]);
    }

    header("Location: payment.php?order_id={$order_id}&paid=1");
    exit;
}
?>

<section class="payment container">
    <h2>Metode Pembayaran</h2>
    <p>Nominal pembayaran: <strong>Rp <?php echo number_format($order['total_amount'],0,',','.'); ?></strong></p>
    <?php if (isset($_GET['paid'])): ?>
        <div class="alert alert-success">
            Terima kasih! Pembayaran Anda tercatat. Silakan unggah bukti jika ada.
        </div>
    <?php endif; ?>
    <form method="post" action="payment.php?order_id=<?php echo $order_id; ?>">
        <div class="form-group">
            <label for="method">Pilih Metode</label>
            <select id="method" name="method" class="form-control" required>
                <option value="qris">QRIS</option>
                <option value="gopay">GoPay</option>
                <option value="ovo">OVO</option>
                <option value="transfer">Transfer Bank</option>
            </select>
        </div>
        <button type="submit" name="submit_payment" class="btn btn-primary mt-3">Submit Pembayaran</button>
    </form>
</section>

<?php
require_once __DIR__ . '/includes/footer.php';
?>

