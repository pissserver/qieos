-- =============================================================
-- 016 - Konversi Unit per Produk
--   * unit_conversions.product_id : konversi sekarang milik satu produk
--     (bukan lagi global per unit base). base_unit otomatis = products.unit.
--   * Hapus konversi lama yang belum punya product_id supaya tidak jadi
--     data yatim (relasi lama berbasis unit base, bukan produk).
-- Aman diulang (cek information_schema).
-- =============================================================

-- 1. Tambah kolom product_id setelah id
SET @exists := (SELECT COUNT(*) FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'unit_conversions' AND COLUMN_NAME = 'product_id');
SET @sql := IF(@exists = 0, 'ALTER TABLE `unit_conversions` ADD COLUMN `product_id` INT(11) DEFAULT NULL AFTER `id`', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 2. Index pencarian per produk
SET @exists := (SELECT COUNT(*) FROM information_schema.STATISTICS
                WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'unit_conversions' AND INDEX_NAME = 'idx_unit_conversions_product');
SET @sql := IF(@exists = 0, 'ALTER TABLE `unit_conversions` ADD INDEX `idx_unit_conversions_product` (`product_id`)', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 3. Bersihkan konversi lama (global, product_id NULL) beserta itemnya
DELETE ci FROM `unit_conversion_items` ci
    JOIN `unit_conversions` c ON c.id = ci.conversion_id
    WHERE c.product_id IS NULL;
DELETE FROM `unit_conversions` WHERE product_id IS NULL;

-- 4. Lepas patokan conversion_id pada produk yang konversinya sudah hilang
UPDATE `products` p
    LEFT JOIN `unit_conversions` c ON c.id = p.conversion_id
    SET p.conversion_id = NULL
    WHERE p.conversion_id IS NOT NULL AND (c.id IS NULL OR c.deleted_at IS NOT NULL);
