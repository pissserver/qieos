-- =============================================================
-- QIEOS - Baseline Skema Database
-- Dibuat otomatis dari server lokal (db_kantin) - 21 tabel.
--
-- Sifat file ini:
--   * Hanya STRUKTUR (tabel & kolom), tidak menyertakan data.
--   * Aman dijalankan berkali-kali (CREATE TABLE IF NOT EXISTS).
--   * Tidak menghapus / mengubah tabel yang sudah ada.
--
-- Cara pakai manual (CLI):
--   mysql -u root -p db_kantin < 000_baseline_schema.sql
-- Atau otomatis lewat runner:  php database/migrate.php
-- =============================================================

-- -------------------------------------------------------------
-- app_settings : penyimpanan setting aplikasi (key-value)
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `app_settings` (
  `name` varchar(100) NOT NULL,
  `value` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------------------
-- chat_messages : chat 1-on-1
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `chat_messages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `read_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_sender` (`sender_id`,`id`),
  KEY `idx_receiver` (`receiver_id`,`id`),
  KEY `idx_receiver_read` (`receiver_id`,`read_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- customers : pelanggan
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `customers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------------------
-- order_details : detail item pesanan
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `order_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `product_combo_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  `price` int(11) DEFAULT NULL,
  `subtotal` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- -------------------------------------------------------------
-- orders : transaksi pesanan
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `staff_id` int(10) NOT NULL,
  `code` varchar(20) NOT NULL,
  `tanggal` date DEFAULT NULL,
  `total` int(11) DEFAULT NULL,
  `status_payment` varchar(15) NOT NULL DEFAULT 'waiting',
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- -------------------------------------------------------------
-- product_combo_items : bahan produk racikan/combine
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `product_combo_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `combo_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL DEFAULT '1',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------------------
-- product_combos : produk racikan/combine
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `product_combos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------------------
-- product_supplier : relasi produk <-> supplier (multi supplier)
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `product_supplier` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- -------------------------------------------------------------
-- products : master produk
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) DEFAULT NULL,
  `code` varchar(50) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `sell_price` decimal(10,2) DEFAULT NULL,
  `unit` varchar(100) DEFAULT NULL,
  `low_stock` int(11) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` int(11) DEFAULT NULL,
  `starred` tinyint(1) NOT NULL DEFAULT '0',
  `catalog` varchar(10) NOT NULL DEFAULT 'nonactive',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- -------------------------------------------------------------
-- purchase_items : detail item pembelian
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `purchase_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `purchase_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  `qty_buy` int(11) DEFAULT NULL,
  `unit` varchar(20) DEFAULT NULL,
  `unit_buy` varchar(20) DEFAULT NULL,
  `remaining_qty` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `price_buy` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- -------------------------------------------------------------
-- purchases : transaksi pembelian
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `purchases` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `form` varchar(50) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- -------------------------------------------------------------
-- sales_stock : ledger pergerakan stok (balance/transfer/sale/return)
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `sales_stock` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) DEFAULT NULL,
  `qty` int(11) DEFAULT '0',
  `type` varchar(20) NOT NULL DEFAULT 'transfer',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- -------------------------------------------------------------
-- stock_request_items : detail item request stok (multi item)
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `stock_request_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `request_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_request` (`request_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- -------------------------------------------------------------
-- stock_requests : header request stok
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `stock_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_by` int(11) DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `approved_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- -------------------------------------------------------------
-- suppliers : pemasok
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `suppliers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `address` text,
  `note` text,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------------------
-- tenant_payments : pembayaran penyewa tenant
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `tenant_payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `staff_id` int(11) NOT NULL,
  `tenant_id` int(10) NOT NULL,
  `cost_payment` float(10,2) NOT NULL,
  `payment_date` date NOT NULL,
  `status` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- -------------------------------------------------------------
-- tenants : data tenant / penyewa
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `tenants` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tenant_name` varchar(20) NOT NULL,
  `tenant_owner` varchar(20) NOT NULL,
  `status` varchar(20) NOT NULL,
  `registration_date` date NOT NULL,
  `deleted_at` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- -------------------------------------------------------------
-- update_details : detail changelog update
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `update_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `update_id` int(20) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- -------------------------------------------------------------
-- updates : changelog update aplikasi
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `updates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `update_name` varchar(30) NOT NULL,
  `update_version` varchar(10) NOT NULL,
  `update_type` enum('major','minor','patch','') NOT NULL,
  `update_date` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- -------------------------------------------------------------
-- users : akun pengguna aplikasi
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fullname` varchar(30) NOT NULL,
  `username` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('developer','administrator','staff kasir','') NOT NULL DEFAULT 'administrator',
  `photo` varchar(255) DEFAULT NULL,
  `created_at` date NOT NULL,
  `last_seen` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- -------------------------------------------------------------
-- utility_payments : pembayaran utilitas tenant
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `utility_payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `staff_id` int(10) NOT NULL,
  `tenant_id` int(20) NOT NULL,
  `cost_payment` float(10,2) NOT NULL,
  `payment_date` date NOT NULL,
  `status` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;