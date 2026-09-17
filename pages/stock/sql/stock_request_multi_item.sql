-- =====================================================================
-- Ubah stock_requests jadi header multi-produk
-- - stock_requests  : header (id, code, status, created_by, approved_by, ...)
-- - stock_request_items : detail item request (request_id, product_id, qty)
-- =====================================================================

-- 1. Tambah kolom code (nomor form request, contoh REQ-0000001)
ALTER TABLE stock_requests
    ADD COLUMN code VARCHAR(20) DEFAULT NULL AFTER id;

-- 2. Generate kode untuk data lama
UPDATE stock_requests SET code = CONCAT('REQ-', LPAD(id,7,'0'));

-- 3. Tabel detail item request
CREATE TABLE IF NOT EXISTS stock_request_items (
    id INT(11) NOT NULL AUTO_INCREMENT,
    request_id INT(11) DEFAULT NULL,
    product_id INT(11) DEFAULT NULL,
    qty INT(11) DEFAULT NULL,
    PRIMARY KEY (id),
    KEY idx_request (request_id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 4. Pindahkan item lama (tiap baris lama = 1 item dalam 1 request)
INSERT INTO stock_request_items (request_id, product_id, qty)
SELECT id, product_id, qty FROM stock_requests;

-- 5. Hapus kolom produk & qty dari header
ALTER TABLE stock_requests
    DROP COLUMN product_id,
    DROP COLUMN qty;

-- 6. code wajib terisi
ALTER TABLE stock_requests
    MODIFY code VARCHAR(20) NOT NULL;