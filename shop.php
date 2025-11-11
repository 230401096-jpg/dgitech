<?php
$page_title = "Shop - DGITECH";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/config.php';

// Fetch products from database
$sql = "SELECT p.id, p.title, p.description, p.price, p.category, p.stock, p.image 
        FROM products p 
        WHERE p.is_active = 1 
        ORDER BY p.id DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="shop-products container">
    <h2>Produk Kami</h2>
    <div class="row">
        <?php foreach ($products as $prod): ?>
        <div class="col-md-4">
            <div class="card">
                <img src="<?php echo '/assets/images/products/' . htmlspecialchars($prod['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($prod['title']); ?>">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($prod['title']); ?></h5>
                    <p class="card-text"><?php echo htmlspecialchars(substr($prod['description'],0,100)) . '...'; ?></p>
                    <p class="price">Rp <?php echo number_format($prod['price'], 0, ',', '.'); ?></p>
                    <a href="/product-detail.php?id=<?php echo $prod['id']; ?>" class="btn btn-primary">Lihat Produk</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
