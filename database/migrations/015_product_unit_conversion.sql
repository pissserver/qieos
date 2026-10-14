-- =============================================================
-- 015 - Produk memakai Konversi Unit
--   * products.conversion_id : grup konversi (unit_conversions) yang
--     dipakai produk. base_unit konversi harus sama dengan products.unit.
--   * hapus products.supplier_id : kolom lama, sudah digantikan tabel
--     relasi product_supplier (multi supplier).
-- Aman diulang (cek information_schema.COLUMNS).
-- =============================================================

-- 1. Tambah kolom conversion_id setelah unit
SET @exists := (SELECT COUNT(*) FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'products' AND COLUMN_NAME = 'conversion_id');
SET @sql := IF(@exists = 0, 'ALTER TABLE `products` ADD COLUMN `conversion_id` INT(11) DEFAULT NULL AFTER `unit`', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 2. Hapus kolom supplier_id (legacy)
SET @exists := (SELECT COUNT(*) FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'products' AND COLUMN_NAME = 'supplier_id');
SET @sql := IF(@exists = 1, 'ALTER TABLE `products` DROP COLUMN `supplier_id`', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
