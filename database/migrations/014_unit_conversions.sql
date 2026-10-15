-- =============================================================
-- 014 - Konversi Unit (panel di bawah Master Unit)
--   * unit_conversions      : satu grup konversi per unit base
--                             (mis. unit base "pcs")
--   * unit_conversion_items : baris konversi "1 <unit> = <qty> <unit base>"
--                             (mis. 1 karton = 100 pcs, 1 rtg = 10 pcs)
-- Satuan disimpan sebagai NAMA (string) mengikuti products.unit.
-- Aman diulang (CREATE TABLE IF NOT EXISTS).
-- =============================================================

-- 1. Tabel unit_conversions (header / unit base)
CREATE TABLE IF NOT EXISTS `unit_conversions` (
    `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
    `base_unit` VARCHAR(100) NOT NULL,
    `created_at` DATETIME DEFAULT NULL,
    `updated_at` DATETIME DEFAULT NULL,
    `deleted_at` DATETIME DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Tabel unit_conversion_items (baris konversi)
CREATE TABLE IF NOT EXISTS `unit_conversion_items` (
    `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
    `conversion_id` INT(11) NOT NULL,
    `unit_name` VARCHAR(100) NOT NULL,
    `qty` DECIMAL(14,4) NOT NULL DEFAULT 1,
    `created_at` DATETIME DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
