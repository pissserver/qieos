-- =============================================================
-- 005 - Request Stok multi-produk
--   * stock_requests        : header (berisi code, no product_id)
--   * stock_request_items   : detail item (request_id, product_id, qty)
-- Mengubah format lama (product_id/qty di header) ke format baru.
-- Aman diulang.
-- =============================================================

-- 1. Tambah kolom code (nomor form request, REQ-0000001) setelah id
SET @exists := (SELECT COUNT(*) FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'stock_requests' AND COLUMN_NAME = 'code');
SET @sql := IF(@exists = 0, 'ALTER TABLE stock_requests ADD COLUMN code VARCHAR(20) DEFAULT NULL AFTER id', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 2. Generate kode untuk data lama yang belum punya code
UPDATE stock_requests SET code = CONCAT('REQ-', LPAD(id,7,'0'))
WHERE code IS NULL OR code = '';

-- 3. Tabel detail item request
CREATE TABLE IF NOT EXISTS stock_request_items (
    id INT(11) NOT NULL AUTO_INCREMENT,
    request_id INT(11) DEFAULT NULL,
    product_id INT(11) DEFAULT NULL,
    qty INT(11) DEFAULT NULL,
    PRIMARY KEY (id),
    KEY idx_request (request_id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 4. Pindahkan item lama (hanya jika kolom product_id masih ada di header)
SET @has_old := (SELECT COUNT(*) FROM information_schema.COLUMNS
                 WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'stock_requests' AND COLUMN_NAME = 'product_id');
SET @sql := IF(@has_old = 0, 'SELECT 1',
   'INSERT INTO stock_request_items (request_id, product_id, qty) SELECT id, product_id, qty FROM stock_requests');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 5. Hapus kolom produk & qty dari header (jika masih ada)
SET @has_old := (SELECT COUNT(*) FROM information_schema.COLUMNS
                 WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'stock_requests' AND COLUMN_NAME = 'product_id');
SET @sql := IF(@has_old = 0, 'SELECT 1', 'ALTER TABLE stock_requests DROP COLUMN product_id, DROP COLUMN qty');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 6. code wajib terisi (jika masih NULL-able)
SET @exists := (SELECT COUNT(*) FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'stock_requests' AND COLUMN_NAME = 'code'
                  AND IS_NULLABLE = 'YES');
SET @sql := IF(@exists = 0, 'SELECT 1', 'ALTER TABLE stock_requests MODIFY code VARCHAR(20) NOT NULL');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;