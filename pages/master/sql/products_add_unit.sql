-- Menambahkan kolom unit (satuan) pada tabel products
-- Kolom bersifat opsional (NULL boleh) dan berada setelah sell_price
ALTER TABLE `products`
    ADD COLUMN `unit` VARCHAR(100) NULL DEFAULT NULL AFTER `sell_price`;