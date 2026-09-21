-- =============================================================
-- 006 - Gabung Daftar Belanja (list_purchases) ke purchases
--   * purchases      : kolom note dihapus (tanggal beli = kolom date)
--   * purchase_items : tambah qty_buy / unit_buy / price_buy,
--                      rename buy_price -> price,
--                      hapus date, unit dibuat nullable
--   * list_purchases / list_purchase_items : tabel lama dihapus
-- Aman diulang.
-- =============================================================

-- 1. purchases: hapus kolom note
SET @exists := (SELECT COUNT(*) FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'purchases' AND COLUMN_NAME = 'note');
SET @sql := IF(@exists = 0, 'SELECT 1', 'ALTER TABLE purchases DROP COLUMN note');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 2. purchase_items: tambah qty_buy (qty rencana belanja) setelah qty
SET @exists := (SELECT COUNT(*) FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'purchase_items' AND COLUMN_NAME = 'qty_buy');
SET @sql := IF(@exists = 0, 'ALTER TABLE purchase_items ADD COLUMN qty_buy INT(11) DEFAULT NULL AFTER qty', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 3. purchase_items: tambah unit_buy (satuan rencana) setelah unit
SET @exists := (SELECT COUNT(*) FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'purchase_items' AND COLUMN_NAME = 'unit_buy');
SET @sql := IF(@exists = 0, 'ALTER TABLE purchase_items ADD COLUMN unit_buy VARCHAR(20) DEFAULT NULL AFTER unit', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 4. purchase_items: hapus kolom date
SET @exists := (SELECT COUNT(*) FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'purchase_items' AND COLUMN_NAME = 'date');
SET @sql := IF(@exists = 0, 'SELECT 1', 'ALTER TABLE purchase_items DROP COLUMN date');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 5. purchase_items: rename buy_price -> price (harga beli aktual)
SET @exists := (SELECT COUNT(*) FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'purchase_items' AND COLUMN_NAME = 'buy_price');
SET @sql := IF(@exists = 0, 'SELECT 1', 'ALTER TABLE purchase_items CHANGE COLUMN buy_price price DECIMAL(10,2) DEFAULT NULL');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 6. purchase_items: tambah price_buy (harga rencana) setelah price
SET @exists := (SELECT COUNT(*) FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'purchase_items' AND COLUMN_NAME = 'price_buy');
SET @sql := IF(@exists = 0, 'ALTER TABLE purchase_items ADD COLUMN price_buy DECIMAL(10,2) DEFAULT NULL AFTER price', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 7. purchase_items: unit dibuat nullable (baris daftar belanja tanpa qty/unit)
SET @exists := (SELECT COUNT(*) FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'purchase_items' AND COLUMN_NAME = 'unit'
                  AND IS_NULLABLE = 'NO');
SET @sql := IF(@exists = 0, 'SELECT 1', 'ALTER TABLE purchase_items MODIFY unit VARCHAR(20) DEFAULT NULL');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 8. Hapus tabel daftar belanja lama
DROP TABLE IF EXISTS list_purchase_items;
DROP TABLE IF EXISTS list_purchases;