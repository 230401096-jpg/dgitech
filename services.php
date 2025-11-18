<?php
$page_title = "Services - DGITECH";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/config.php';

// Fetch services from database
$sql = "SELECT s.id, s.title, s.description, s.price, s.mitra_id 
    FROM services s 
        WHERE s.is_active = 1 
        ORDER BY s.id DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="services-list container">
    <h2>Layanan Kami</h2>
    <div class="row">
        <?php foreach ($services as $srv): ?>
        <div class="col-md-4">
            <div class="card">
                <img src="/assets/images/services/service-default.jpg" class="card-img-top" alt="<?php echo htmlspecialchars($srv['title']); ?>">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($srv['title']); ?></h5>
                    <p class="card-text"><?php echo htmlspecialchars(substr($srv['description'],0,100)) . '...'; ?></p>
                    <p class="price">Mulai Rp <?php echo number_format($srv['price'] ?? ($srv['base_price'] ?? 0), 0, ',', '.'); ?></p>
                    <a href="/service-detail.php?id=<?php echo $srv['id']; ?>" class="btn btn-primary">Lihat Detil</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
