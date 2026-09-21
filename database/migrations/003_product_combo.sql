-- =============================================================
-- 003 - Fitur Racikan / Combine Produk
--   * product_combos      : paket racikan (mis: Kopi Susu Gula)
--   * product_combo_items : bahan penyusun paket
--   * order_details       : tambah product_combo_id + name
-- Aman diulang.
-- =============================================================

-- 1. Tabel product_combos
CREATE TABLE IF NOT EXISTS product_combos (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    deleted_at INT(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Tabel product_combo_items
CREATE TABLE IF NOT EXISTS product_combo_items (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    combo_id INT(11) NOT NULL,
    product_id INT(11) NOT NULL,
    qty INT(11) NOT NULL DEFAULT 1,
    price DECIMAL(10,2) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. order_details.product_combo_id (setelah product_id)
SET @exists := (SELECT COUNT(*) FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'order_details' AND COLUMN_NAME = 'product_combo_id');
SET @sql := IF(@exists = 0, 'ALTER TABLE order_details ADD COLUMN product_combo_id INT(11) NULL AFTER product_id', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 4. order_details.name (setelah product_combo_id)
SET @exists := (SELECT COUNT(*) FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'order_details' AND COLUMN_NAME = 'name');
SET @sql := IF(@exists = 0, 'ALTER TABLE order_details ADD COLUMN name VARCHAR(255) NULL AFTER product_combo_id', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;