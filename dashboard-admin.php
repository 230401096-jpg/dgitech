<?php
$page_title = "Dashboard Admin - DGITECH";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth-check.php';

if ($_SESSION['user_role'] !== 'admin') {
    header("Location: /login.php");
    exit;
}

// Ambil statistik dasar
$stmt = $conn->query("SELECT COUNT(*) AS total_users FROM users");
$totalUsers = $stmt->fetch(PDO::FETCH_ASSOC)['total_users'];

$stmt2 = $conn->query("SELECT COUNT(*) AS total_orders FROM orders");
$totalOrders = $stmt2->fetch(PDO::FETCH_ASSOC)['total_orders'];

?>

<section class="dashboard container">
    <h2>Selamat datang, Administrator</h2>
    <div class="admin-stats">
        <div class="stat-box">
            <h4>Total Pengguna</h4>
            <p><?php echo $totalUsers; ?></p>
        </div>
        <div class="stat-box">
            <h4>Total Pesanan</h4>
            <p><?php echo $totalOrders; ?></p>
        </div>
    </div>
</section>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
