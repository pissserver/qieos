-- Menambahkan kolom batas stok menipis (low_stock) pada tabel products
-- Status stok: 0 = Habis, 0 < total <= low_stock = Menipis, total > low_stock = Ready
ALTER TABLE `products`
    ADD COLUMN `low_stock` INT NULL DEFAULT NULL AFTER `unit`;

-- Pastikan low_stock bisa NULL untuk kategori Additional
-- (opsional: jalankan baris ini jika kolom sudah ada sebagai NOT NULL)
ALTER TABLE `products` MODIFY `low_stock` INT NULL DEFAULT NULL;

-- Tabel penyimpanan pengaturan aplikasi (key-value)
-- Digunakan untuk setting global, misal: low_stock_default
CREATE TABLE IF NOT EXISTS `app_settings` (
    `name`  VARCHAR(100) NOT NULL,
    `value` VARCHAR(255) NULL,
    PRIMARY KEY (`name`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- Nilai awal default batas stok menipis (5)
INSERT INTO `app_settings` (`name`, `value`)
VALUES ('low_stock_default', '5')
ON DUPLICATE KEY UPDATE `value` = `value`;