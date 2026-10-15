-- =============================================================
-- 011 - purchase_items: tambah unit_price_buy (harga satuan belanja)
-- Aman diulang.
-- =============================================================

SET @exists := (SELECT COUNT(*) FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'purchase_items' AND COLUMN_NAME = 'unit_price_buy');
SET @sql := IF(@exists = 0, 'ALTER TABLE purchase_items ADD COLUMN unit_price_buy DECIMAL(10,2) DEFAULT NULL AFTER unit_buy', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
