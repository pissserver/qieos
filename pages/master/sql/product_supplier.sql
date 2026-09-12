-- Tabel relasi produk ke supplier (support multi supplier)
CREATE TABLE IF NOT EXISTS `product_supplier` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `product_id` INT NOT NULL,
    `supplier_id` INT NOT NULL,
    `created_at` DATETIME DEFAULT NULL,
    UNIQUE KEY `uq_product_supplier` (`product_id`, `supplier_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;