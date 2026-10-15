-- =============================================================
-- 010 - Produk: kolom low_stock_kantin (batas menipis per-produk
-- untuk layer kantin). Bila NULL, dipakai default global
-- app_settings.low_stock_kantin_default.
-- Aman diulang (pengecekan kolom via information_schema).
-- =============================================================

SET @exists := (SELECT COUNT(*) FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'products' AND COLUMN_NAME = 'low_stock_kantin');
SET @sql := IF(@exists = 0, 'ALTER TABLE `products` ADD COLUMN `low_stock_kantin` INT NULL DEFAULT NULL AFTER `low_stock`', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
