-- =============================================================
-- 009 - Batas stok menipis kantin (default global)
-- Batas stok gudang tetap per-produk (products.low_stock) dengan
-- fallback ke app_settings.low_stock_default. Batas stok kantin
-- dikelola global lewat app_settings.low_stock_kantin_default.
-- Aman diulang (ON DUPLICATE KEY).
-- =============================================================

INSERT INTO `app_settings` (`name`, `value`)
VALUES ('low_stock_kantin_default', '5')
ON DUPLICATE KEY UPDATE `value` = `value`;
