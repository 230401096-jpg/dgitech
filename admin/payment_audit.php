<?php
$page_title = "Payment Audit - DGITECH";
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth-check.php';

if ($_SESSION['user_role'] !== 'admin') {
    header("Location: /login.php");
    exit;
}

$stmt = $conn->prepare('SELECT pa.id, pa.payment_id, pa.admin_id, pa.action, pa.note, pa.created_at, p.reference, p.amount, u.name AS user_name, a.name AS admin_name FROM payment_audit pa LEFT JOIN payments p ON pa.payment_id = p.id LEFT JOIN users u ON p.user_id = u.id LEFT JOIN users a ON pa.admin_id = a.id ORDER BY pa.created_at DESC LIMIT 200');
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<section class="container">
    <h2>Riwayat Verifikasi Pembayaran</h2>
    <?php if (count($rows) === 0): ?>
        <p>Tidak ada data audit.</p>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Payment Ref</th>
                    <th>User</th>
                    <th>Admin</th>
                    <th>Action</th>
                    <th>Note</th>
                    <th>Waktu</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $r): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($r['id']); ?></td>
                        <td><?php echo htmlspecialchars($r['reference'] ?? '—'); ?></td>
                        <td><?php echo htmlspecialchars($r['user_name'] ?? '—'); ?></td>
                        <td><?php echo htmlspecialchars($r['admin_name'] ?? '—'); ?></td>
                        <td><?php echo htmlspecialchars($r['action']); ?></td>
                        <td><?php echo htmlspecialchars($r['note'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($r['created_at']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
