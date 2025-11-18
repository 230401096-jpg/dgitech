<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DgiTech - Digital IT Service Platform</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 600px;
        }
        h1 {
            color: #333;
            margin-bottom: 20px;
            font-size: 2.5em;
        }
        .status {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
            border-left: 4px solid #667eea;
        }
        .team-list {
            text-align: left;
            margin: 20px 0;
        }
        .team-list li {
            margin: 8px 0;
            padding: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 DgiTech Platform</h1>

        <p class="lead">Selamat datang di repo DGITECH — ringkasan halaman dan API yang tersedia di proyek ini.</p>

        <div class="status">
            <strong>Status:</strong> 🟢 Development Mode
        </div>

        <section style="margin-top:1rem;text-align:left">
            <h3>Halaman Utama</h3>
            <ul>
                <li><a href="/shop.php">Shop</a> — Daftar produk</li>
                <li><a href="/services.php">Services</a> — Daftar layanan mitra</li>
                <li><a href="/cart.php">Cart</a> — Keranjang</li>
                <li><a href="/checkout.php">Checkout</a></li>
                <li><a href="/payment.php">Payment</a> — Upload bukti pembayaran</li>
                <li><a href="/dashboard-admin.php">Admin Dashboard</a></li>
                <li><a href="/dashboard-customer.php">Customer Dashboard</a></li>
                <li><a href="/dashboard-mitra.php">Mitra Dashboard</a></li>
                <li><a href="/login.php">Login</a> / <a href="/register.php">Register</a></li>
                <li><a href="/about.php">About</a> / <a href="/contact.php">Contact</a></li>
            </ul>
        </section>

        <section style="margin-top:1rem;text-align:left">
            <h3>API Endpoints</h3>
            <ul>
                <li><code>/api/get-products.php</code></li>
                <li><code>/api/create-order.php</code></li>
                <li><code>/api/get-orders.php</code></li>
                <li><code>/api/get-services.php</code></li>
                <li><code>/api/get-payments-pending.php</code></li>
            </ul>
        </section>

        <section style="margin-top:1rem;text-align:left">
            <h3>Repository</h3>
            <ul>
                <li><code>includes/</code> — konfigurasi, helper, AI agent</li>
                <li><code>database/dgitech_schema.sql</code> — file schema utama</li>
                <li><code>assets/</code> — CSS, images, vendor</li>
                <li><code>admin/</code> — halaman admin (verifikasi, audit)</li>
                <li><code>api/</code> — API endpoints</li>
                <li><code>scripts/</code> — utilitas (seed, runner)</li>
            </ul>
        </section>

        <p style="margin-top:1rem"><strong>Tip:</strong> Jalankan server lokal dengan <code>php -S 0.0.0.0:8000 -t /workspaces/dgitech</code> lalu buka <a href="http://localhost:8000">http://localhost:8000</a></p>
    </div>
</body>
</html>
