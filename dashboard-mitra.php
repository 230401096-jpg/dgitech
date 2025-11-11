<?php
$page_title = "Dashboard Mitra - DGITECH";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth-check.php';

if ($_SESSION['user_role'] !== 'mitra') {
    header("Location: /login.php");
    exit;
}

$mitraId = $_SESSION['user_id'];
// Ambil layanan mitra
$stmt = $conn->prepare("SELECT * FROM services WHERE mitra_id = :mid ORDER BY created_at DESC");
$stmt->execute([':mid' => $mitraId]);
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ambil produk mitra
$stmt2 = $conn->prepare("SELECT * FROM products WHERE mita_id = :mid ORDER BY created_at DESC");
$stmt2->execute([':mid' => $mitraId]);
$products = $stmt2->fetchAll(PDO::FETCH_ASSOC);

?>

<section class="dashboard container">
    <h2>Halo, <?php echo htmlspecialchars($_SESSION['user_name']); ?></h2>
    <h3>Layanan Anda</h3>
    <ul>
        <?php foreach ($services as $s): ?>
            <li><?php echo htmlspecialchars($s['title']); ?> &ndash; Rp <?php echo number_format($s['base_price'],0,',','.'); ?></li>
        <?php endforeach; ?>
    </ul>
    <h3>Produk Anda</h3>
    <ul>
        <?php foreach ($products as $p): ?>
            <li><?php echo htmlspecialchars($p['title']); ?> &ndash; Rp <?php echo number_format($p['price'],0,',','.'); ?></li>
        <?php endforeach; ?>
    </ul>
</section>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
