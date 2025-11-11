<?php
$page_title = "Checkout - DGITECH";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/config.php';

// Pastikan user login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Pastikan keranjang tidak kosong
if (empty($_SESSION['cart'])) {
    echo "<p>Keranjang Anda kosong. <a href='/shop.php'>Belanja sekarang</a></p>";
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

// Hitung total
$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['qty'];
}

// Submit checkout
if (isset($_POST['proceed'])) {
    // Insert order
    $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, shipping_method, status, created_at) VALUES (:uid, :total, :ship, 'pending', NOW())");
    $stmt->execute([
        ':uid' => $_SESSION['user_id'],
        ':total' => $total,
        ':ship' => $_POST['shipping_method']
    ]);
    $order_id = $conn->lastInsertId();
    // Insert items
    foreach ($_SESSION['cart'] as $pid => $item) {
        $stmt2 = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (:oid, :pid, :qty, :price)");
        $stmt2->execute([
            ':oid' => $order_id,
            ':pid' => $pid,
            ':qty' => $item['qty'],
            ':price' => $item['price']
        ]);
    }
    // Kosongkan keranjang
    unset($_SESSION['cart']);
    header("Location: payment.php?order_id=" . $order_id);
    exit;
}
?>

<section class="checkout container">
    <h2>Checkout</h2>
    <form method="post" action="checkout.php">
        <div class="form-group">
            <label for="shipping_method">Metode Pengantaran</label>
            <select id="shipping_method" name="shipping_method" class="form-control">
                <option value="gojek">Gojek</option>
                <option value="grab">Grab</option>
                <option value="maxim">Maxim</option>
                <option value="pickup">Ambil Sendiri</option>
            </select>
        </div>
        <div class="form-group mt-3">
            <p>Total Pembayaran: <strong>Rp <?php echo number_format($total,0,',','.'); ?></strong></p>
        </div>
        <button type="submit" name="proceed" class="btn btn-success">Lanjut ke Pembayaran</button>
    </form>
</section>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
