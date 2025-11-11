<?php
$page_title = "Dashboard Customer - DGITECH";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth-check.php';

// Pastikan role customer
if ($_SESSION['user_role'] !== 'customer') {
    header("Location: /login.php");
    exit;
}

$userId = $_SESSION['user_id'];
// Ambil riwayat order produk
$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = :uid ORDER BY created_at DESC");
$stmt->execute([':uid' => $userId]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<section class="dashboard container">
    <h2>Halo, <?php echo htmlspecialchars($_SESSION['user_name']); ?></h2>
    <h3>Riwayat Pesanan Anda</h3>
    <table class="table table-striped">
        <thead>
        <tr><th>ID</th><th>Tanggal</th><th>Total</th><th>Status</th></tr>
        </thead>
        <tbody>
        <?php foreach ($orders as $o): ?>
            <tr>
                <td><?php echo $o['id']; ?></td>
                <td><?php echo $o['created_at']; ?></td>
                <td>Rp <?php echo number_format($o['total_amount'],0,',','.'); ?></td>
                <td><?php echo htmlspecialchars($o['status']); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
