-- =============================================================
-- 018 - Hapus kolom `name` dari tabel order_details
--
-- Item pesanan kini selalu mengacu ke produk lewat `product_id`.
-- Racikan (combo) dipecah jadi baris per bahan penyusun
-- (`product_id` + `product_combo_id`), jadi kolom `name` tidak
-- dipakai lagi.
--
-- Idempotent: cek information_schema dulu supaya aman kalau kolom
-- sudah hilang (mis. pada DB yang di-recreate dari 000_schema.sql).
-- =============================================================

SET @has_name := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'order_details'
    AND COLUMN_NAME = 'name'
);

SET @drop_sql := IF(@has_name > 0,
  'ALTER TABLE `order_details` DROP COLUMN `name`',
  'SELECT 1'
);

PREPARE drop_stmt FROM @drop_sql;
EXECUTE drop_stmt;
DEALLOCATE PREPARE drop_stmt;
