# DGITECH  
Platform Penjualan & Servis Laptop, PC, Printer & Komponen

## 1. Informasi Proyek  
- **Judul Proyek:** DGITECH – Platform Penjualan & Servis Laptop/PC  
- **Nama Anggota Kelompok:** Mhd. Qadri, Sukri Hamdi, Dion, Andhika, Septian Zalukhu  
- **Dosen Pengampu:** [Rahmad Firdaus, S.Kom., M.TI]  
- **Tanggal Mulai Proyek:** 11 November 2025  
- **Tanggal Selesai Proyek (Estimasi):** 18 November 2025  
- **Deskripsi Singkat Proyek:**  
  Platform DGITECH adalah sebuah website yang menghubungkan pelanggan, mitra teknisi, dan admin untuk penjualan perangkat (baru / bekas / komponen) serta layanan servis (laptop, PC, printer). Fitur utamanya meliputi manajemen produk & layanan, sistem pembayaran (QRIS / GoPay / OVO / transfer), pengantaran perangkat, pencatatan modal & laba/rugi, serta AI Agent yang memberikan insight otomatis.

---

## 2. Rencana Kerja dan Tugas Tiap Anggota  
### • Pembagian Tugas:  
| Nama Anggota        | Tugas yang Dikerjakan                          | Deadline Tugas        | Status Tugas              |
|----------------------|------------------------------------------------|------------------------|----------------------------|
| Mhd. Qadri          | Ketua & Integrator: Struktur proyek, deployment, dokumentasi, integrasi antar-modul | 18 Nov 2025            | [Belum / Dalam Proses / Selesai] |
| Dion         | Backend Dev A: Auth & user, produk & layanan CRUD, `shop.php`, `services.php`       | 18 Nov 2025            | [Status]                   |
| Sukri Hamdi                 | Backend Dev B: Transaksi, pembayaran, pembukuan & AI                                   | 18 Nov 2025            | [Status]                   |
| Andhika              | Frontend Dev A: UI/UX: index, slider, card produk/layanan                               | 18 Nov 2025            | [Status]                   |
| Septian Zalukhu      | Frontend Dev B + Dashboard UI: customer, mitra, admin dashboards                          | 18 Nov 2025            | [Status]                   |

---

## 3. Log Pengerjaan Proyek (Tanggal, Aktivitas, Progres)  
| Nama Anggota        | Tugas yang Dikerjakan                          | Deadline Tugas        | Status Tugas              |
|----------------------|------------------------------------------------|------------------------|----------------------------|
| …                    | …                                              | …                      | …                          |

---

## 4. Kendala yang Dihadapi dan Solusi yang Diterapkan  
| Tanggal     | Kendala yang Dihadapi                          | Solusi / Tindakan yang Diterapkan                   |
|-------------|------------------------------------------------|-----------------------------------------------------|
| …           | …                                              | …                                                   |

---

## 5. Refleksi dan Evaluasi Pengerjaan Proyek  
- **Apa yang sudah berjalan dengan baik?**  
  …  
- **Apa yang perlu diperbaiki atau ditingkatkan?**  
  …  
- **Rencana ke Depan**  
  …

---

## 6. Dokumentasi Pendukung  
(Wajib! Tambahkan gambar, diagram, screenshot, atau laporan pengujian yang relevan dengan proyek.)

---

## 7. Tanda Tangan dan Konfirmasi Pengerjaan  
- **Nama Anggota:** …  
- **Tanda Tangan:** _______________________  
- **Tanggal:** …

---

## Panduan Teknis Penerapan Web Dgitech

### A. Menjalankan Web Secara Lokal  
1. Install XAMPP (atau alternatif Apache + MySQL).  
2. Aktifkan Apache dan MySQL.  
3. Tempatkan kode proyek di folder `htdocs/` (misal `htdocs/dgitech`).  
4. Buat database MySQL via phpMyAdmin, lalu impor `database/dgitech.sql`.  
5. Edit `includes/config.php` agar sesuai dengan kredensial lokal:  
   ```php
   <?php
   $host = 'localhost';
   $db   = 'dgitech_db';
   $user = 'root';
   $pass = '';
   try {
     $conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
   } catch (PDOException $e) {
     die("Koneksi gagal: " . $e->getMessage());
   }
   ?>

  ## AI Otomasi Pembukuan

  Ringkasan
  - File `includes/ai_agent.php` berisi fungsi `analyzeFinances()` yang:
    - mengambil data dari tabel `financial_records` untuk 30 hari terakhir,
    - memanggil layanan OpenAI untuk menghasilkan insight bisnis singkat,
    - menyimpan hasil insight ke tabel `ai_insights`.

  Persiapan
  1. Buat tabel di database menggunakan skrip contoh: `database/create_ai_tables.sql`.
     Jalankan di MySQL:
     ```bash
     mysql -u user -p dgitech_db < database/create_ai_tables.sql
     ```

  2. Atur kunci API OpenAI sebagai environment variable (direkomendasikan):
     - Untuk sesi sementara (pengujian lokal):
       ```bash
       export OPENAI_API_KEY='sk-...'
       ```
     - Untuk produksi/cron: set environment variable di konfigurasi service (systemd, panel hosting, atau pool php-fpm).

  3. Uji secara lokal:
     ```bash
     php scripts/run_ai_agent.php
     ```

  Contoh cron (jalan tiap hari jam 02:10):
  ```
  10 2 * * * cd /path/to/dgitech && /usr/bin/php scripts/run_ai_agent.php >> /var/log/dgitech/ai_agent.log 2>&1
  ```
  Pastikan environment cron memiliki `OPENAI_API_KEY`. Contoh men-sourcing profile sebelum menjalankan:
  ```
  10 2 * * * . /home/youruser/.profile && cd /path/to/dgitech && /usr/bin/php scripts/run_ai_agent.php >> /var/log/dgitech/ai_agent.log 2>&1
  ```

  Catatan & Praktik Terbaik
  - Jangan menyimpan atau commit `OPENAI_API_KEY` ke repository.
  - Batasi frekuensi pemanggilan (mis. harian) untuk mengendalikan biaya token OpenAI.
  - Tambahkan mekanisme retry/backoff untuk kegagalan sementara (transient errors).
  - Simpan metadata usage (mis. jumlah token/response length) jika ingin monitoring biaya.
  - Gunakan user database dengan hak minimal (SELECT/INSERT yang dibutuhkan).

  File terkait
  - `includes/ai_agent.php` — fungsi analisis dan panggilan OpenAI.
  - `scripts/run_ai_agent.php` — runner yang bisa dipanggil dari cron.
  - `database/create_ai_tables.sql` — contoh skema tabel `financial_records` dan `ai_insights`.

  Jika ingin, saya dapat menambahkan integrasi `.env` untuk pengembangan lokal, atau menambahkan retry/backoff dan pencatatan penggunaan token.
