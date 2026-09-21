-- =============================================================
-- 001 - Produk: kolom unit & low_stock + tabel app_settings
-- Aman diulang (menggunakan pengecekan kolom via information_schema).
-- =============================================================

-- 1. Tambah kolom unit (setelah sell_price)
SET @exists := (SELECT COUNT(*) FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'products' AND COLUMN_NAME = 'unit');
SET @sql := IF(@exists = 0, 'ALTER TABLE `products` ADD COLUMN `unit` VARCHAR(100) NULL DEFAULT NULL AFTER `sell_price`', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 2. Tambah kolom low_stock (setelah unit)
SET @exists := (SELECT COUNT(*) FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'products' AND COLUMN_NAME = 'low_stock');
SET @sql := IF(@exists = 0, 'ALTER TABLE `products` ADD COLUMN `low_stock` INT NULL DEFAULT NULL AFTER `unit`', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 3. Tabel penyimpanan setting aplikasi (key-value)
CREATE TABLE IF NOT EXISTS `app_settings` (
    `name`  VARCHAR(100) NOT NULL,
    `value` VARCHAR(255) NULL,
    PRIMARY KEY (`name`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- 4. Nilai awal default batas stok menipis (5)
INSERT INTO `app_settings` (`name`, `value`)
VALUES ('low_stock_default', '5')
ON DUPLICATE KEY UPDATE `value` = `value`;