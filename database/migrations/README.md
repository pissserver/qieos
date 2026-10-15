# QIEOS - Migrasi Database

Satu file skema: `000_schema.sql` — struktur lengkap 25 tabel `db_kantin`
(diasumsikan sesuai database produksi saat ini).

## Cara pakai

Dari folder project (XAMPP aktif):

```
php database/migrate.php
```

atau double-click `database/migrate.bat` di Windows.

Runner akan:

1. Membuat database `db_kantin` bila belum ada.
2. Menjalankan file `*.sql` yang belum pernah dicatat di tabel `schema_migrations`.
3. File yang sudah dijalankan otomatis dilewati (skip).

Setelah `git pull`, jalankan lagi perintah yang sama.

## Aturan penamaan

- Skema baru = file SQL baru berurutan (`001_...sql`, `002_...sql`, dst).
- Selalu pakai `CREATE TABLE IF NOT EXISTS` / `ALTER TABLE` aman-ulang
  supaya tidak merusak data yang sudah ada.
- Jangan mengedit file migrasi yang sudah pernah dijalankan di server lain —
  buat file baru.

## Reset total (hanya untuk development)

Kalau ingin membangun ulang skema dari nol:

1. Hapus baris di tabel `schema_migrations` (atau drop tabelnya).
2. Edit `000_schema.sql` bila struktur sudah berubah.
3. Jalankan lagi `php database/migrate.php`.
