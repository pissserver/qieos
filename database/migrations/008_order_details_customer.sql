-- =============================================================
-- 008 - Relasi customer pada order_details
--   order_details.customer_id : referensi customers.id
--   Diletakkan SEBELUM qty sesuai urutan kolom yang dipakai aplikasi.
--   NULL = pesanan tanpa customer (umumnya penjualan walk-in).
-- Aman diulang.
-- =============================================================

SET @exists := (SELECT COUNT(*) FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'order_details' AND COLUMN_NAME = 'customer_id');
SET @sql := IF(@exists = 0, 'ALTER TABLE order_details ADD COLUMN customer_id INT(11) NULL AFTER name', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Index pendukung pencarian pesanan per customer
SET @exists := (SELECT COUNT(*) FROM information_schema.STATISTICS
                WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'order_details' AND INDEX_NAME = 'idx_order_details_customer');
SET @sql := IF(@exists = 0, 'ALTER TABLE order_details ADD INDEX idx_order_details_customer (customer_id)', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
