<?php
$page_title = "Detail Layanan - DGITECH";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/config.php';

if (!isset($_GET['id'])) {
    echo "<p>Layanan tidak ditemukan.</p>";
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$service_id = intval($_GET['id']);
$sql = "SELECT s.id, s.title, s.description, s.base_price, u.name as mitra_name
        FROM services s
        LEFT JOIN users u ON s.mitra_id = u.id
        WHERE s.id = :sid AND s.is_active = 1";
$stmt = $conn->prepare($sql);
$stmt->execute([':sid' => $service_id]);
$service = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$service) {
    echo "<p>Layanan tidak ditemukan.</p>";
    require_once __DIR__ . '/includes/footer.php';
    exit;
}
?>

<section class="service-detail container">
    <div class="row">
        <div class="col-md-7">
            <h2><?php echo htmlspecialchars($service['title']); ?></h2>
            <p>Penyedia: <?php echo htmlspecialchars($service['mitra_name'] ?? 'DGITECH Admin'); ?></p>
            <p><?php echo nl2br(htmlspecialchars($service['description'])); ?></p>
            <p class="price">Mulai Rp <?php echo number_format($service['base_price'], 0, ',', '.'); ?></p>
            <a href="/checkout.php?service_id=<?php echo $service['id']; ?>" class="btn btn-success">Pesan Layanan</a>
        </div>
        <div class="col-md-5">
            <img src="/assets/images/services/service-default.jpg" class="img-fluid" alt="<?php echo htmlspecialchars($service['title']); ?>">
        </div>
    </div>
</section>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
