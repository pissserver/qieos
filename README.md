# Qieos — POS Management System

**Qieos** adalah sistem manajemen POS (_Point of Sale_) berbasis web yang dirancang untuk pengelolaan operasional kantin & pasar modern. Aplikasi ini mencakup pengelolaan transaksi penjualan, stok gudang (FIFO), pengadaan/pembelian barang, pengelolaan tenant, rekap harian, hingga laporan keuangan dan pencetakan PDF/Excel.

---

## 🚀 Fitur Utama

### 🏠 Dashboard & Navigasi
- **Dashboard Real-Time**: Ringkasan omzet hari ini, pesanan terbayar/menunggu, dan akses cepat ke menu utama
- **Pencarian Global** (Ctrl+K): Cari halaman, produk, supplier, customer, pesanan, dan tenant secara instan
- **What's New**: Pop-up notifikasi pembaruan sistem terbaru dari navbar
- **Chat Antar User**: Komunikasi real-time antara kasir, administrator, dan developer dengan notifikasi pesan baru
- **Keranjang Navbar**: Belanja langsung dari katalog dengan keranjang yang terintegrasi di navbar
- **Omzet Real-Time**: Tampilan omzet hari ini di navbar dengan auto-refresh

### 📦 Master Data
- **Master Produk**: Kelola produk lengkap dengan kode, kategori, harga, stok minimal, foto, dan status katalog
- **Master Unit**: Sistem konversi satuan multi-level (pcs, karton, slop, dus, lusin, kg, liter) dengan konversi otomatis per produk
- **Master Supplier**: Data pemasok barang dengan relasi many-to-many ke produk
- **Master Customer**: Database pelanggan dengan riwayat transaksi
- **Master User**: Manajemen akun dengan 3 role (Developer, Administrator, Staff Kasir)
- **Paket Racikan**: Buat paket bundling produk dengan harga khusus

### 🛒 Purchasing & Stok
- **Belanja & Restock**: Daftar rencana pembelian dengan status tracking (draft → approved → completed)
- **Detail Stok Gudang**: Monitoring stok berbasis FIFO per layer tanggal/harga dengan histori lengkap
- **Transfer Stok**: Transfer antar gudang atau gudang ke kantin dengan approval system
- **Request Stok**: Kasir mengajukan pengisian stok dari gudang (pending/approved/rejected)

### 💰 Kasir & Penjualan (POS)
- **Katalog Produk**: Interface kasir dengan filter kategori, produk favorit (starred), dan gambar produk
- **Sistem Keranjang**: Tambah produk, atur qty, pilih customer opsional
- **Checkout & Struk**: Generate kode pesanan unik, cetak struk otomatis saat pembayaran
- **Manajemen Pesanan**: Tandai terbayar, edit customer, batalkan pesanan, lihat detail & reprint struk
- **Stok Kantin**: Monitoring stok kasir dengan alert low stock

### 🏢 Tenant Management
- **Pendaftaran Tenant**: Input data tenant penyewa dengan kartu identitas siap cetak
- **Detail Tenant**: Rincian penyewaan, status pembayaran sewa & utilitas
- **Pembayaran Sewa**: Pencatatan pembayaran bulanan dengan struk
- **Pembayaran Utilitas**: Tagihan listrik & air dengan struk terpisah

### 📊 Rekap & Laporan
- **Rekap Harian**: Ringkasan penjualan & tenant dalam satu tampilan
- **Laporan Penjualan**: Analisis lengkap dengan grafik omzet, produk terlaris, kategori, margin, pengeluaran (PDF/Excel)
- **Laporan Tenant**: Rekap pembayaran sewa & utilitas per periode (PDF/Excel)

### 🛠️ Sistem & Dokumentasi
- **Update Log**: Changelog rilis aplikasi (major, minor, patch) dengan detail perubahan
- **Guide**: Petunjuk lengkap penggunaan setiap halaman sesuai role
- **Database Schema**: Diagram relasi 24 tabel interaktif dengan keterangan lengkap

---

## 🔄 Alur Kerja Aplikasi (Workflow)

```
[1. Login User]
      │
      ├──> Developer / Admin ──> Purchasing (Beli Barang) ──> Masuk Stok Gudang (FIFO)
      │                                                               │
      │                                                     Transfer Stok ke Kasir
      │                                                               │
      │                                                               ▼
      └──> Staff Kasir ───────> Katalog Produk (Kasir) ──────> Sales Stock (Siap Jual)
                                       │
                                   Checkout
                                       │
                                       ▼
                                 Transaksi Paid ──> Cetak Struk
                                       │
                                       ▼
                              [Rekap & Laporan] <── [Pembayaran Tenant & Utility]
```

1. **Login & Session**: User masuk sesuai akun. Role menentukan menu navigasi yang dapat diakses.
2. **Purchasing & Inbound**: Admin/Developer melakukan pembelian barang → data masuk ke `purchase_items` sebagai layer stok gudang (tracking `remaining_qty` per lot).
3. **Stock Transfer**: Barang dari gudang ditransfer ke stok penjualan (`sales_stock`) melalui sistem request transfer dengan approval.
4. **Penjualan Kasir**: Kasir memilih produk di katalog, isi keranjang, checkout. Pesanan dibuat berstatus `waiting`; stok terpotong **saat dibayar** (tombol Bayar). Omzet hanya menghitung pesanan `paid`.
5. **Manajemen Tenant**: Kasir/Admin catat pembayaran sewa dan tagihan utilitas tenant.
6. **Laporan & Rekap**: Data transaksi dan pembayaran tenant dapat direkap harian dan dicetak sebagai PDF/Excel.

---

## 👥 Hak Akses User (User Roles)

Sistem memiliki **3 tingkatan user role**:

| Fitur / Modul                            | Developer | Administrator |   Staff Kasir    |
| ---------------------------------------- | :-------: | :-----------: | :--------------: |
| **Dashboard**                            |    ✅     |      ✅       |        ✅        |
| **Pencarian Global & Chat**              |    ✅     |      ✅       |        ✅        |
| **Master Produk**                        |    ✅     |   🔜 Segera   |        ❌        |
| **Master Unit**                          |    ✅     |   🔜 Segera   |        ❌        |
| **Master Supplier**                      |    ✅     |   🔜 Segera   |        ❌        |
| **Master Customer**                      |    ✅     |   🔜 Segera   |   🔜 Segera      |
| **Master User**                          |    ✅     |      ✅       |        ❌        |
| **Paket Racikan**                        |    ✅     |      ✅       |        ❌        |
| **Belanja & Restock**                    |    ✅     |   🔜 Segera   |        ❌        |
| **Detail Stok Gudang**                   |    ✅     |   🔜 Segera   |        ❌        |
| **Transfer Stok**                        |    ✅     |   🔜 Segera   |        ❌        |
| **Stok Kantin**                          |    ✅     |      ❌       |   🔜 Segera      |
| **Katalog Produk (POS)**                 |    ✅     |      ❌       |   🔜 Segera      |
| **Pesanan (Checkout & Bayar)**           |    ✅     |      ❌       |   🔜 Segera      |
| **Batal Pesanan**                        |    ✅     |      ❌       |        ❌        |
| **Tenant (Pendaftaran & Detail)**        |    ✅     |      ✅       |   ✅ (View)      |
| **Pembayaran Tenant (Input)**            |    ❌     |      ❌       |        ✅        |
| **Pembayaran Tenant (Edit/Hapus)**       |    ✅     |      ✅       |        ❌        |
| **Rekap Penjualan & Tenant**             |    ✅     |      ❌       |        ✅        |
| **Laporan Penjualan & Tenant**           |    ✅     |   🔜 Segera   |        ❌        |
| **Update Log (Changelog)**               | ✅ (CRUD) |   👁️ (View)   |    👁️ (View)     |
| **Guide & Database Schema**              |    ✅     |      ✅       |        ✅        |

_🔜 = Coming Soon (dalam pengembangan, diperkirakan rilis akhir 2026)_

_Catatan:_ Akun **Developer** bersifat terproteksi dan tidak dapat diubah/dihapus oleh pengguna lain.

---

## 🗄️ Database & Relasi Tabel

Nama Database: **`db_kantin`** (MySQL)

### Daftar Tabel (24 Tabel)

| Domain | Tabel | Fungsi |
|--------|-------|--------|
| **Master** | `products` | Master produk: kode, nama, kategori, harga jual, unit, konversi, low stock, foto |
| | `suppliers` | Master supplier / pemasok barang |
| | `customers` | Master customer pembeli di kantin |
| | `users` | Akun pengguna: developer, administrator, staff kasir |
| | `tenants` | Data tenant penyewa kantin |
| | `units` | Master satuan barang (pcs, karton, slop, kg, liter, dll) |
| **Konversi Unit** | `unit_conversions` | Header konversi unit per produk (base unit) |
| | `unit_conversion_items` | Detail konversi: unit + qty relatif ke base unit |
| **Master Lanjutan** | `product_combos` | Paket racikan produk |
| | `product_combo_items` | Bahan penyusun paket racikan |
| | `product_supplier` | Relasi produk ke supplier (many-to-many) |
| **Purchasing** | `purchases` | Transaksi pembelian / daftar belanja |
| | `purchase_items` | Detail item pembelian: qty, unit, harga (FIFO via `remaining_qty`) |
| **Stok** | `sales_stock` | Ledger stok: balance, transfer, sale, return — saldo = SUM(qty) |
| | `stock_requests` | Permintaan stok kantin → gudang (pending/approved/rejected) |
| | `stock_request_items` | Detail produk dalam permintaan stok |
| **Penjualan** | `orders` | Header pesanan: kode, tanggal, kasir, total, status bayar |
| | `order_details` | Item pesanan: produk/paket, qty, harga, customer |
| **Tenant** | `tenant_payments` | Pembayaran sewa tenant per periode |
| | `utility_payments` | Pembayaran utilitas (listrik/air) tenant |
| **Sistem** | `chat_messages` | Pesan chat antar pengguna dengan status baca |
| | `updates` | Riwayat rilis aplikasi (major/minor/patch) |
| | `update_details` | Detail perubahan per rilis |

Lihat halaman **Database Schema** di aplikasi untuk diagram relasi interaktif lengkap.

---

## 🛠️ Cara Membuka & Menjalankan Aplikasi di Lokal

### Persyaratan Sistem

- Web Server: **Apache** (XAMPP / WampServer / Laragon)
- Bahasa Pemrograman: **PHP 8.0+**
- Database: **MySQL / MariaDB**
- Ekstensi PHP: `mysqli`, `gd`, `mbstring`, `json`
- Composer (untuk library Dompdf & PHPExcel)

### Langkah-Langkah Instalasi (XAMPP)

1. **Unduh / Clone Project**
   Letakkan folder proyek di dalam direktori `htdocs` XAMPP:

    ```bash
    cd C:\xampp\htdocs
    git clone https://github.com/pissserver/qieos.git
    ```

2. **Install Dependencies**
   Jalankan Composer untuk install library PDF & Excel:

    ```bash
    cd qieos
    composer install
    ```

3. **Jalankan Apache & MySQL**
   Buka **XAMPP Control Panel**, lalu klik **Start** pada modul **Apache** dan **MySQL**.

4. **Setup Database** (via phpMyAdmin)

    - Buka `http://localhost/phpmyadmin`
    - Buat database baru dengan nama `db_kantin`
    - Import file SQL schema (file akan disediakan setelah versi final)

5. **Cek Konfigurasi Koneksi**
   Buka file `script/connection.php` dan pastikan pengaturan sesuai dengan MySQL lokal Anda:

    ```php
    $servername = "localhost";
    $username   = "root";
    $password   = "";        // kosongkan jika default XAMPP
    $dbname     = "db_kantin";
    ```

6. **Akses Aplikasi di Browser**
   Buka browser dan akses:
    ```
    http://localhost/qieos
    ```

---

## 📁 Struktur Folder Proyek

```
qieos/
├── assets/                  # Upload foto profil user & produk, brand assets
├── css/                     # Stylesheet CSS per halaman (40+ file modular)
│   └── pages/
├── pages/                   # Modul & halaman utama sistem
│   ├── chat/                # Chat antar user
│   ├── components/          # Sidebar, navbar, modals, tables, AJAX endpoints
│   │   ├── data/            # API search global, data endpoints
│   │   ├── modals/          # Modal components
│   │   └── tables/          # Table components
│   ├── master/              # Master produk, unit, supplier, customer, user, paket racikan
│   ├── other/               # Update log, guide, database schema, coming soon
│   ├── profile/             # Profil user, upload foto, ganti password
│   ├── recap/               # Rekap harian penjualan & tenant
│   ├── report/              # Laporan penjualan & tenant (PDF/Excel)
│   │   ├── sales/           # Sub-laporan penjualan
│   │   └── tenant/          # Sub-laporan tenant
│   ├── sales/               # POS: katalog, pesanan, checkout, stok kasir
│   ├── stock/               # Gudang: stok FIFO, mutasi, transfer, restock
│   └── tenant/              # Pendaftaran, detail, pembayaran sewa & utilitas
├── script/                  # Connection.php (koneksi DB), headscript, footscript
├── sessions/                # Autentikasi: login, register, session guard, logout
├── vendor/                  # Library Composer (Dompdf, PHPExcel)
├── .htaccess                # Rewrite rules & security
├── index.php                # Landing/login page
├── manifest.php             # PWA manifest (Progressive Web App ready)
├── sw.js                    # Service Worker (offline support)
└── README.md                # Dokumentasi proyek
```

---

## 🎨 Teknologi & Library

- **Frontend**: HTML5, CSS3 (custom modular), JavaScript (vanilla), Bootstrap Icons, Font Awesome
- **Backend**: PHP 8.0+ (procedural & OOP hybrid), MySQLi
- **Database**: MySQL / MariaDB dengan 24 tabel relasional
- **PDF**: Dompdf (laporan & struk)
- **Excel**: PHPExcel (export data master & laporan)
- **PWA**: Service Worker + Manifest (offline-capable)

---

## 🔐 Keamanan

- Session-based authentication (guard di `sessions/session.php`)
- Role-based access control (RBAC) per halaman sesuai role user
- Prepared statements (MySQLi) pada query data utama untuk mencegah SQL injection
- Password disimpan dengan hashing `password_hash()`
- Akun **Developer** terproteksi (tidak dapat dihapus/diubah oleh user lain)

---

## 📄 Lisensi & Hak Cipta

Dikembangkan oleh **REYQIE**.  
Qieos POS Management System.

---

## 📞 Kontak & Dukungan

- **GitHub**: [github.com/pissserver/qieos](https://github.com/pissserver/qieos)
- **Issues**: [github.com/pissserver/qieos/issues](https://github.com/pissserver/qieos/issues)
