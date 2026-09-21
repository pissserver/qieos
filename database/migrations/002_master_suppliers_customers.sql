-- =============================================================
-- 002 - Master Supplier, Customer & relasi product_supplier
-- Aman diulang (CREATE TABLE IF NOT EXISTS + cek kolom).
-- =============================================================

-- 1. Tabel suppliers
CREATE TABLE IF NOT EXISTS `suppliers` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(50) DEFAULT NULL,
    `address` TEXT DEFAULT NULL,
    `note` TEXT DEFAULT NULL,
    `created_at` DATETIME DEFAULT NULL,
    `updated_at` DATETIME DEFAULT NULL,
    `deleted_at` DATETIME DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Tabel customers
CREATE TABLE IF NOT EXISTS `customers` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(50) DEFAULT NULL,
    `created_at` DATETIME DEFAULT NULL,
    `updated_at` DATETIME DEFAULT NULL,
    `deleted_at` DATETIME DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Tabel relasi produk ke supplier (multi supplier)
CREATE TABLE IF NOT EXISTS `product_supplier` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `product_id` INT NOT NULL,
    `supplier_id` INT NOT NULL,
    `created_at` DATETIME DEFAULT NULL,
    UNIQUE KEY `uq_product_supplier` (`product_id`, `supplier_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Kolom supplier_id pada products (setelah sell_price)
SET @exists := (SELECT COUNT(*) FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'products' AND COLUMN_NAME = 'supplier_id');
SET @sql := IF(@exists = 0, 'ALTER TABLE `products` ADD COLUMN `supplier_id` INT DEFAULT NULL AFTER `sell_price`', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 5. Kolom deleted_at pada products (soft delete)
SET @exists := (SELECT COUNT(*) FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'products' AND COLUMN_NAME = 'deleted_at');
SET @sql := IF(@exists = 0, 'ALTER TABLE `products` ADD COLUMN `deleted_at` INT DEFAULT NULL AFTER `created_at`', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;