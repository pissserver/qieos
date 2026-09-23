# Qieos — POS Management System

**Qieos** adalah sistem manajemen POS (_Point of Sale_) berbasis web yang dirancang untuk pengelolaan operasional kantin & pasar modern. Aplikasi ini mencakup pengelolaan transaksi penjualan, stok gudang (FIFO), pengadaan/pembelian barang, pengelolaan tenant, rekap harian, hingga laporan keuangan dan pencetakan PDF/Excel.

---

## 🚀 Fitur Utama

- **Dashboard Real-Time**: Ringkasan total pendapatan, pengeluaran, laba bersih, total pesanan, grafik omzet 7 hari, dan perbandingan bulanan.
- **Kasir & Penjualan (POS)**: Katalog produk dengan gambar & filter kategori, sistem keranjang belanja, checkout transaksi, dan cetak struk.
- **Pengadaan & Pembelian (Purchasing)**: Pencatatan daftar belanja, input transaksi pembelian barang, dan produk tambahan.
- **Manajemen Stok Gudang (FIFO)**: Pengelolaan stok gudang berbasis layer tanggal/harga, mutasi stok, dan transfer stok ke kasir penjualan.
- **Pengelolaan Tenant**: Pendaftaran tenant, detail tenant, serta pencatatan pembayaran sewa dan biaya air & listrik (utility).
- **Laporan & Rekap**: Laporan penjualan dan tenant dengan filter periode tanggal, cetak PDF resmi (Dompdf), dan export ke Excel.
- **Manajemen User**: Pengelolaan akun user dengan 3 tingkatan hak akses (Developer, Administrator, Staff Kasir).
- **Changelog & Update**: Riwayat pembaruan versi sistem (_major, minor, patch_).

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
2. **Purchasing & Inbound**: Admin/Developer melakukan pembelian barang → data masuk ke `purchase_items` sebagai layer stok gudang (dengan tracking `remaining_qty` per lot).
3. **Stock Transfer**: Barang dari gudang ditransfer ke stok penjualan (`sales_stock`) melalui sistem persetujuan request transfer.
4. **Penjualan Kasir**: Kasir memilih produk di katalog, mengisi keranjang, dan melakukan checkout. Stok penjualan otomatis terpotong (`sales_stock`).
5. **Manajemen Tenant**: Kasir/Admin mencatat pembayaran sewa dan tagihan air/listrik tenant.
6. **Laporan & Rekap**: Data transaksi dan pembayaran tenant dapat direkap harian serta dicetak sebagai dokumen PDF / Excel.

---

## 👥 Kepemilikan User & Hak Akses (User Roles)

Sistem memiliki **3 tingkatan user role**:

| Fitur / Modul                            | Developer | Administrator |   Staff Kasir    |
| ---------------------------------------- | :-------: | :-----------: | :--------------: |
| **Dashboard**                            |    ✅     |      ✅       |        ✅        |
| **Purchasing** (Belanja & Pembelian)     |    ✅     |      ✅       |        ❌        |
| **Gudang Stok** (Stok, Mutasi, Transfer) |    ✅     |      ✅       |        ❌        |
| **Penjualan (POS)** (Katalog & Checkout) |    ✅     |      ❌       |        ✅        |
| **Pendaftaran Tenant**                   |    ✅     |      ✅       |        ❌        |
| **Manajemen Tenant & Detail**            |    ✅     |      ✅       |        ✅        |
| **Pembayaran Tenant (Input)**            |    ❌     |      ❌       |        ✅        |
| **Pembayaran Tenant (Edit/Hapus)**       |    ✅     |      ✅       | ❌ (Cetak Struk) |
| **Rekap Penjualan & Tenant**             |    ✅     |      ❌       |        ✅        |
| **Laporan Penjualan & Tenant**           |    ✅     |      ✅       |        ❌        |
| **Manajemen User (Admin & Kasir)**       |    ✅     |      ✅       |        ❌        |
| **Batal Pesanan (Cancel Order)**         |    ✅     |      ❌       |        ❌        |
| **Manajemen Update Log**                 | ✅ (CRUD) |   👁️ (View)   |    👁️ (View)     |

_Catatan:_ Akun **Developer** bersifat terproteksi dan tidak dapat diubah/dihapus oleh pengguna lain di dalam sistem.

---

## 🗄️ Database & Relasi Tabel

Nama Database: **`db_kantin`** (MySQL)

### Daftar Tabel (13 Tabel)

1. **`users`**: Data akun pengguna (username, password bcrypt, fullname, role, photo).
2. **`products`**: Master data produk (nama, kode, kategori, harga jual, foto, status katalog, starred).
3. **`purchases`**: Header transaksi pembelian & daftar belanja (nomor form, tanggal).
4. **`purchase_items`**: Detail item pembelian dengan tracking stok FIFO (`qty`, `remaining_qty`, `price`) serta data rencana belanja (`qty_buy`, `unit_buy`, `price_buy`).
5. **`sales_stock`**: Tabel pergerakan stok kantin (ledger). Setiap baris mencatat satu pergerakan (`type`: `balance`, `transfer`, `sale`, `return`) dengan qty positif/negatif; saldo saat ini = `SUM(qty)` per produk.
6. **`stock_requests`**: Permintaan transfer stok dari kasir ke gudang (`pending`, `approved`, `rejected`).
7. **`orders`**: Header transaksi penjualan (kode transaksi, staff_id, total, status_payment).
8. **`order_details`**: Detail item penjualan (order_id, product_id, qty, price, subtotal).
9. **`tenants`**: Data penyewa / tenant (nama tenant, pemikir/owner, status).
10. **`tenant_payments`**: Transaksi pembayaran uang sewa tenant.
11. **`utility_payments`**: Transaksi pembayaran biaya air & listrik tenant.
12. **`updates`**: Header changelog versi aplikasi.
13. **`update_details`**: Detail uraian pembaruan versi.

### Diagram Relasi Sederhana

```
users ──(staff_id)──> orders ──(order_id)──> order_details ──(product_id)──> products
  │                                                                                │
  ├──(staff_id)──> tenant_payments <──(tenant_id)── tenants                      │
  └──(staff_id)──> utility_payments <──(tenant_id)──┘                            │
                                                                                   │
purchases ──(purchase_id)──> purchase_items ──────(product_id)─────────────────────┤
                                                                                     ├─> sales_stock
updates ────> update_details                                                       └─> stock_requests
```

---

## 🛠️ Cara Membuka & Menjalankan Aplikasi di Lokal

### Persyaratan Sistem

- Web Server: **Apache** (XAMPP / WampServer / Laragon)
- Bahasa Pemrograman: **PHP 8.0+**
- Database: **MySQL / MariaDB**
- Ekstensi PHP: `mysqli`, `gd`, `mbstring`, `json`

### Langkah-Langkah Instalasi (XAMPP)

1. **Unduh / Clone Project**
   Letakkan folder proyek di dalam direktori `htdocs` XAMPP:

    ```bash
    cd C:\xampp\htdocs
    git clone https://github.com/pissserver/qieos.git
    ```

2. **Jalankan Apache & MySQL**
   Buka **XAMPP Control Panel**, lalu klik **Start** pada modul **Apache** dan **MySQL**.

3. **Impor Database**
    - Buka browser dan akses `http://localhost/phpmyadmin`
    - Buat database baru bernama **`db_kantin`**
    - Pilih database `db_kantin`, masuk ke tab **Import**, lalu pilih file SQL database aplikasi (`db_kantin.sql`).
    - Klik **Go / Kirim** hingga proses impor selesai.

    _Atau via Command Line:_

    ```bash
    mysql -u root -e "CREATE DATABASE db_kantin;"
    mysql -u root db_kantin < db_kantin.sql
    ```

4. **Cek Konfigurasi Koneksi**
   Buka file `script/connection.php` dan pastikan pengaturan sesuai dengan MySQL lokal Anda:

    ```php
    $servername = "localhost";
    $username   = "root";
    $password   = "";        // kosongkan jika default XAMPP
    $dbname     = "db_kantin";
    ```

5. **Akses Aplikasi di Browser**
   Buka browser dan buka alamat:
    ```
    http://localhost/qieos
    ```

---

## 📁 Struktur Folder Proyek

```
qieos/
├── assets/                  # Asset gambar brand, logo, & upload foto profil/produk
├── css/                     # Stylesheet CSS eksternal terpisah per halaman
│   └── pages/
├── pages/                   # Modul & halaman utama sistem
│   ├── components/          # Navigation (sidebar, navbar) & AJAX data endpoints
│   ├── master/              # Master data (produk, supplier, customer) & master user
│   ├── other/               # Modul update & changelog
│   ├── profile/             # Pengelolaan profil user
│   ├── purchasing/          # Modul daftar belanja & input pembelian
│   ├── recap/               # Rekap harian penjualan & tenant
│   ├── report/              # Laporan penjualan & tenant (PDF & Excel)
│   ├── sales/               # Modul POS (katalog, pesanan, checkout, stok kasir)
│   ├── stock/               # Modul gudang (stok FIFO, mutasi, transfer)
│   └── tenant/              # Modul pendaftaran, detail, & pembayaran tenant
├── script/                  # Koneksi DB ($conn) & file pemicu headscript/footscript
├── sessions/                # Sistem otentikasi (login, register, session guard, logout)
└── vendor/                  # Library Composer (Dompdf, PHPExcel)
```

---

## 📄 Lisensi & Hak Cipta

Dikembangkan oleh **REYQIE**.  
Qieos POS Management System.
