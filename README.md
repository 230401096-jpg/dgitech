# DGITECH

DGITECH adalah proyek contoh platform e-commerce + layanan (PHP + MySQL) yang berisi:

- Halaman front-end (Shop, Product, Services, Checkout, Payment)
- Panel admin untuk verifikasi pembayaran dan audit
- API sederhana untuk membuat order dan mengambil data
- AI agent (opsional) untuk analisis finansial (dengan OpenAI)

---

## Struktur Project

Ringkasan file/dir penting:

- `index.php` — halaman beranda (role-aware)
- `shop.php`, `product-detail.php` — tampilan produk
- `services.php`, `service-detail.php` — layanan mitra
- `payment.php` — halaman upload bukti pembayaran
- `dashboard-admin.php`, `dashboard-customer.php`, `dashboard-mitra.php` — dashboard sesuai role
- `admin/verify_payment.php` — endpoint admin untuk verify pembayaran
- `admin/payment_audit.php` — lihat riwayat verifikasi
- `api/` — berisi API endpoints (create-order, get-orders, dll.)
- `includes/` — konfigurasi, helper, mail, AI agent
- `database/dgitech_schema.sql` — skema database lengkap (gunakan ini untuk membuat DB)
- `assets/` — CSS, JS, vendor placeholders
- `uploads/payments/` — tempat menyimpan bukti pembayaran (pastikan writeable)

---

## Setup Lokal (pengembang)

1. Clone repo

```bash
git clone https://github.com/230401096-jpg/dgitech.git
cd dgitech
```

2. Siapkan `.env`

Salin `.env.example` ke `.env` dan sesuaikan nilai. Contoh untuk dev:

```
DB_HOST=127.0.0.1
DB_NAME=dgitech_db
DB_USER=dgitech
DB_PASS=dgitechpwd
SMTP_HOST=127.0.0.1
SMTP_PORT=1025
MAIL_FROM="DGITECH" <no-reply@localhost>
OPENAI_API_KEY=
```

3. Jalankan MySQL (lokal atau Docker)

Opsi Docker (direkomendasikan):

```bash
docker run --name dgitech-mysql -e MYSQL_ROOT_PASSWORD=rootpwd -e MYSQL_DATABASE=dgitech_db -e MYSQL_USER=dgitech -e MYSQL_PASSWORD=dgitechpwd -p 3306:3306 -v "$PWD/database:/docker-entrypoint-initdb.d" -d mysql:8.0
```

Jika Anda sudah punya server MySQL, impor skema:

```bash
mysql -u DB_USER -p DB_NAME < database/dgitech_schema.sql
```

4. Install dependencies (PHPMailer) — optional

```bash
composer install
```

5. (Optional) Jalankan MailHog untuk menangkap email lokal

```bash
docker compose -f docker-compose.mailhog.yml up -d
# buka http://localhost:8025
```

6. Jalankan server PHP built-in untuk pengujian

```bash
php -S 0.0.0.0:8000 -t .
# buka http://localhost:8000
```

---

## Penggunaan & Admin

- Buat akun admin menggunakan `scripts/seed_admin.php` atau langsung insert ke DB.
- Login sebagai admin → buka `dashboard-admin.php` untuk melihat pembayaran yang menunggu verifikasi.
- Saat admin memverifikasi, sistem akan menulis ke tabel `payment_audit` dan mengirim email (PHPMailer atau mail()).

## Endpoints API

- `POST /api/create-order.php` — buat order baru (JSON)
- `GET /api/get-products.php` — daftar produk
- `GET /api/get-orders.php` — daftar order user
- `GET /api/get-services.php` — daftar layanan
- `GET /api/get-payments-pending.php` — (admin) daftar pembayaran pending

## Notes / Catatan Pengembang

- Jaga agar file `.env` tidak di-commit. Gunakan secrets di server produksi.
- Untuk produksi, gunakan web server (Nginx/Apache + PHP-FPM) dan HSTS, TLS, Content Security Policy.
- Pertimbangkan menggunakan Monolog untuk logging, dan PHPUnit untuk test.
- AI agent menggunakan OpenAI — pastikan kunci API aman dan biaya dipertimbangkan.

---

Jika Anda ingin saya mengatur deployment otomatis (GitHub Actions -> server atau Fly.io), beri tahu target hosting Anda.
