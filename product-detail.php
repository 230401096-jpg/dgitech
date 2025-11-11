<?php
$page_title = "Detail Produk - DGITECH";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/config.php';

if (!isset($_GET['id'])) {
    echo "<p>Produk tidak ditemukan.</p>";
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$product_id = intval($_GET['id']);
$sql = "SELECT p.id, p.title, p.description, p.price, p.cost, p.stock, p.category, p.image, u.name as mitra_name
        FROM products p
        LEFT JOIN users u ON p.mitra_id = u.id
        WHERE p.id = :pid AND p.is_active = 1";
$stmt = $conn->prepare($sql);
$stmt->execute([':pid' => $product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo "<p>Produk tidak ditemukan.</p>";
    require_once __DIR__ . '/includes/footer.php';
    exit;
}
?>

<section class="product-detail container">
    <div class="row">
        <div class="col-md-5">
            <img src="<?php echo '/assets/images/products/' . htmlspecialchars($product['image']); ?>" class="img-fluid" alt="<?php echo htmlspecialchars($product['title']); ?>">
        </div>
        <div class="col-md-7">
            <h2><?php echo htmlspecialchars($product['title']); ?></h2>
            <p>Penjual: <?php echo htmlspecialchars($product['mitra_name'] ?? 'DGITECH Admin'); ?></p>
            <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
            <p class="price">Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></p>
            <p><strong>Stok:</strong> <?php echo htmlspecialchars($product['stock']); ?></p>
            <a href="/cart.php?add=<?php echo $product['id']; ?>" class="btn btn-success">Tambah ke Keranjang</a>
        </div>
    </div>
</section>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
