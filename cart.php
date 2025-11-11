<?php
$page_title = "Keranjang Belanja - DGITECH";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/config.php';

// Start keranjang di session jika belum ada
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Tambah produk ke keranjang
if (isset($_GET['add'])) {
    $prod_id = intval($_GET['add']);
    // Ambil data produk
    $stmt = $conn->prepare("SELECT id, title, price FROM products WHERE id = :pid AND is_active = 1");
    $stmt->execute([':pid' => $prod_id]);
    $prod = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($prod) {
        // jika sudah ada, increment quantity
        if (isset($_SESSION['cart'][$prod_id])) {
            $_SESSION['cart'][$prod_id]['qty'] += 1;
        } else {
            $_SESSION['cart'][$prod_id] = [
                'title' => $prod['title'],
                'price' => $prod['price'],
                'qty'   => 1
            ];
        }
    }
    header("Location: cart.php");
    exit;
}

// Hapus produk dari keranjang
if (isset($_GET['remove'])) {
    $prod_id = intval($_GET['remove']);
    if (isset($_SESSION['cart'][$prod_id])) {
        unset($_SESSION['cart'][$prod_id]);
    }
    header("Location: cart.php");
    exit;
}

// Update quantity jika form di-submit
if (isset($_POST['update'])) {
    foreach ($_POST['qty'] as $pid => $quantity) {
        $pid = intval($pid);
        $quantity = intval($quantity);
        if ($quantity <= 0) {
            unset($_SESSION['cart'][$pid]);
        } else {
            $_SESSION['cart'][$pid]['qty'] = $quantity;
        }
    }
    header("Location: cart.php");
    exit;
}
?>

<section class="cart container">
    <h2>Keranjang Belanja Anda</h2>
    <?php if (empty($_SESSION['cart'])): ?>
        <p>Keranjang Anda kosong.</p>
        <a href="/shop.php" class="btn btn-primary">Belanja Sekarang</a>
    <?php else: ?>
        <form method="post" action="cart.php">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Harga</th>
                    <th>Jumlah</th>
                    <th>Subtotal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php 
            $total = 0;
            foreach ($_SESSION['cart'] as $pid => $item): 
                $subtotal = $item['price'] * $item['qty'];
                $total += $subtotal;
            ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['title']); ?></td>
                    <td>Rp <?php echo number_format($item['price'],0,',','.'); ?></td>
                    <td>
                        <input type="number" name="qty[<?php echo $pid; ?>]" value="<?php echo $item['qty']; ?>" min="1" style="width:70px;">
                    </td>
                    <td>Rp <?php echo number_format($subtotal,0,',','.'); ?></td>
                    <td><a href="cart.php?remove=<?php echo $pid; ?>" class="btn btn-sm btn-danger">Hapus</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3">Total</th>
                    <th colspan="2">Rp <?php echo number_format($total,0,',','.'); ?></th>
                </tr>
            </tfoot>
        </table>
        <button type="submit" name="update" class="btn btn-secondary">Update Jumlah</button>
        <a href="/checkout.php" class="btn btn-success">Checkout</a>
        </form>
    <?php endif; ?>
</section>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
