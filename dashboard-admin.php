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

<section class="container pending-payments">
    <h3>Pembayaran Menunggu Verifikasi</h3>
    <div id="pending-payments-root">
        <p>Memuat daftar pembayaran...</p>
    </div>
</section>

<script>
async function fetchPendingPayments() {
    const root = document.getElementById('pending-payments-root');
    root.innerHTML = '<p>Memuat daftar pembayaran...</p>';
    try {
        const res = await fetch('/api/get-payments-pending.php', { credentials: 'same-origin' });
        const data = await res.json();
        if (!data.success) {
            root.innerHTML = '<p>Gagal memuat data.</p>';
            return;
        }
        if (data.data.length === 0) {
            root.innerHTML = '<p>Tidak ada pembayaran menunggu verifikasi.</p>';
            return;
        }
        let html = '<table class="table"><thead><tr><th>ID</th><th>Referensi</th><th>User</th><th>Jumlah</th><th>Bukti</th><th>Dibuat</th><th>Aksi</th></tr></thead><tbody>';
        for (const p of data.data) {
            const proofLink = p.proof ? `<a href="/${p.proof}" target="_blank">Lihat Bukti</a>` : '—';
            html += `<tr><td>${p.id}</td><td>${p.reference}</td><td>${p.user_name || p.user_email}</td><td>${p.amount}</td><td>${proofLink}</td><td>${p.created_at}</td><td><button data-id="${p.id}" class="approve">Setuju</button> <button data-id="${p.id}" class="reject">Tolak</button></td></tr>`;
        }
        html += '</tbody></table>';
        root.innerHTML = html;

        root.querySelectorAll('button.approve').forEach(btn => {
            btn.addEventListener('click', () => handleAction(btn.dataset.id, 'approve'));
        });
        root.querySelectorAll('button.reject').forEach(btn => {
            btn.addEventListener('click', () => handleAction(btn.dataset.id, 'reject'));
        });
    } catch (err) {
        root.innerHTML = '<p>Terjadi kesalahan saat memuat data.</p>';
        console.error(err);
    }
}

async function handleAction(paymentId, action) {
    if (!confirm('Yakin akan ' + (action === 'approve' ? 'menyetujui' : 'menolak') + ' pembayaran ini?')) return;
    try {
        const res = await fetch('/admin/verify_payment.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            credentials: 'same-origin',
            body: JSON.stringify({ payment_id: paymentId, action })
        });
        const data = await res.json();
        if (data.success) {
            alert('Berhasil: ' + data.status);
            fetchPendingPayments();
        } else {
            alert('Gagal: ' + (data.error || 'Unknown'));
        }
    } catch (err) {
        console.error(err);
        alert('Terjadi kesalahan jaringan');
    }
}

document.addEventListener('DOMContentLoaded', fetchPendingPayments);
</script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
