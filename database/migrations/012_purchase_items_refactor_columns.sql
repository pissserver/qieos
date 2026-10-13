-- =============================================================
-- 012 - purchase_items: refactor kolom untuk kejelasan semantik
--   * qty → qty_stock (qty stok aktual masuk gudang)
--   * unit → unit_stock (satuan stok di gudang)
--   * price → price_unit (harga satuan pembelian)
--   * unit_price_buy → dihapus (diganti dengan price_unit)
--   * Urutan: qty_buy, qty_stock, remaining_qty, unit_buy, unit_stock, price_unit, price_buy
-- Aman diulang.
-- =============================================================

-- 1. Hapus unit_price_buy
SET @exists := (SELECT COUNT(*) FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'purchase_items' AND COLUMN_NAME = 'unit_price_buy');
SET @sql := IF(@exists = 0, 'SELECT 1', 'ALTER TABLE purchase_items DROP COLUMN unit_price_buy');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 2. Rename qty → qty_stock
SET @exists := (SELECT COUNT(*) FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'purchase_items' AND COLUMN_NAME = 'qty');
SET @sql := IF(@exists = 0, 'SELECT 1', 'ALTER TABLE purchase_items CHANGE COLUMN qty qty_stock INT(11) DEFAULT NULL');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 3. Rename unit → unit_stock
SET @exists := (SELECT COUNT(*) FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'purchase_items' AND COLUMN_NAME = 'unit');
SET @sql := IF(@exists = 0, 'SELECT 1', 'ALTER TABLE purchase_items CHANGE COLUMN unit unit_stock VARCHAR(20) DEFAULT NULL');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 4. Rename price → price_unit
SET @exists := (SELECT COUNT(*) FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'purchase_items' AND COLUMN_NAME = 'price');
SET @sql := IF(@exists = 0, 'SELECT 1', 'ALTER TABLE purchase_items CHANGE COLUMN price price_unit DECIMAL(10,2) DEFAULT NULL');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 5. Susun ulang kolom: qty_buy → qty_stock → remaining_qty → unit_buy → unit_stock → price_unit → price_buy
SET @exists := (SELECT COUNT(*) FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'purchase_items' AND COLUMN_NAME = 'qty_stock');
SET @sql := IF(@exists = 0, 'SELECT 1', 'ALTER TABLE purchase_items MODIFY COLUMN qty_stock INT(11) DEFAULT NULL AFTER qty_buy');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exists := (SELECT COUNT(*) FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'purchase_items' AND COLUMN_NAME = 'remaining_qty');
SET @sql := IF(@exists = 0, 'SELECT 1', 'ALTER TABLE purchase_items MODIFY COLUMN remaining_qty INT(11) DEFAULT NULL AFTER qty_stock');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exists := (SELECT COUNT(*) FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'purchase_items' AND COLUMN_NAME = 'unit_buy');
SET @sql := IF(@exists = 0, 'SELECT 1', 'ALTER TABLE purchase_items MODIFY COLUMN unit_buy VARCHAR(20) DEFAULT NULL AFTER remaining_qty');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exists := (SELECT COUNT(*) FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'purchase_items' AND COLUMN_NAME = 'unit_stock');
SET @sql := IF(@exists = 0, 'SELECT 1', 'ALTER TABLE purchase_items MODIFY COLUMN unit_stock VARCHAR(20) DEFAULT NULL AFTER unit_buy');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exists := (SELECT COUNT(*) FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'purchase_items' AND COLUMN_NAME = 'price_unit');
SET @sql := IF(@exists = 0, 'SELECT 1', 'ALTER TABLE purchase_items MODIFY COLUMN price_unit DECIMAL(10,2) DEFAULT NULL AFTER unit_stock');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exists := (SELECT COUNT(*) FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'purchase_items' AND COLUMN_NAME = 'price_buy');
SET @sql := IF(@exists = 0, 'SELECT 1', 'ALTER TABLE purchase_items MODIFY COLUMN price_buy DECIMAL(10,2) DEFAULT NULL AFTER price_unit');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
