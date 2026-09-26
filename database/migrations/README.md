# QIEOS - Migrasi Database

Semua file SQL untuk membangun / memperbarui struktur database `db_kantin`
disatukan di folder ini. File dijalankan **berurutan sesuai nama** dan sudah
didesain **aman dijalankan ulang** (idempotent).

## Cara 1 - Otomatis (disarankan)

Pastikan MySQL & PHP aktif (XAMPP), lalu dari folder project:

```
php database/migrate.php
```

atau di Windows: double-click `database/migrate.bat`

Runner akan:
1. Membuat database `db_kantin` bila belum ada.
2. Membuat tabel log `schema_migrations`.
3. Menjalankan setiap file `*.sql` yang belum pernah dijalankan.
4. File yang sudah pernah dijalankan akan otomatis dilewati (skip).

Jadi setiap kali selesai `git pull`, tinggal jalankan lagi migrator ini.

## Cara 2 - Manual (via mysql CLI)

```
mysql -u root -p db_kantin < database/migrations/000_baseline_schema.sql
mysql -u root -p db_kantin < database/migrations/001_products_unit_low_stock.sql
mysql -u root -p db_kantin < database/migrations/002_master_suppliers_customers.sql
...
```

Catatan: tanpa runner, Anda harus memastikan manual file mana yang belum
dijalankan di server tersebut.

## Isi folder

| File | Isi |
|------|-----|
| `000_baseline_schema.sql` | Skema lengkap 21 tabel produksi (hanya struktur, tanpa data) |
| `001_products_unit_low_stock.sql` | Kolom `unit` & `low_stock` + tabel `app_settings` |
| `002_master_suppliers_customers.sql` | `suppliers`, `customers`, `product_supplier` |
| `003_product_combo.sql` | Racikan/combine produk + kolom di `order_details` |
| `004_chat.sql` | `chat_messages` + kolom `last_seen` di `users` |
| `005_stock_request_multi_item.sql` | Request stok multi-produk |
| `006_purchases_list_migration.sql` | Gabung daftar belanja ke `purchases` |
| `007_stock_ledger_sales.sql` | Ledger pergerakan stok (balance/transfer/sale/return) |