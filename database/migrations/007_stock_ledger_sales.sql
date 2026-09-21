-- =============================================================
-- 007 - Gabung stock_transfers ke sales_stock (ledger pergerakan stok)
--   sales_stock mencatat SETIAP pergerakan stok per baris:
--     balance  : saldo awal sebelum migrasi
--     transfer : stok masuk dari gudang
--     sale     : stok keluar karena penjualan
--     return   : stok kembali karena pembatalan pesanan
-- Aman diulang.
-- =============================================================

-- 1. Tambah kolom type (setelah qty)
SET @exists := (SELECT COUNT(*) FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sales_stock' AND COLUMN_NAME = 'type');
SET @sql := IF(@exists = 0, 'ALTER TABLE sales_stock ADD COLUMN type VARCHAR(20) NOT NULL DEFAULT ''transfer'' AFTER qty', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 2. Tambah kolom created_at (setelah type)
SET @exists := (SELECT COUNT(*) FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sales_stock' AND COLUMN_NAME = 'created_at');
SET @sql := IF(@exists = 0, 'ALTER TABLE sales_stock ADD COLUMN created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER type', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 3. Saldo lama yang masih ber-type 'transfer' dijadikan baris awal (opening balance)
UPDATE sales_stock SET type = 'balance' WHERE type = 'transfer';

-- 4. Kolom updated_at tidak dipakai lagi (baris bersifat immutable)
SET @exists := (SELECT COUNT(*) FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sales_stock' AND COLUMN_NAME = 'updated_at');
SET @sql := IF(@exists = 0, 'SELECT 1', 'ALTER TABLE sales_stock DROP COLUMN updated_at');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 5. Hapus tabel log lama
DROP TABLE IF EXISTS stock_transfers;