-- =============================================================
-- 013 - Master Unit (satuan barang: pcs, karton, slop, dus, ...)
-- Aman diulang (CREATE TABLE IF NOT EXISTS + seed hanya bila kosong).
-- =============================================================

-- 1. Tabel units
CREATE TABLE IF NOT EXISTS `units` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `note` VARCHAR(255) DEFAULT NULL,
    `created_at` DATETIME DEFAULT NULL,
    `updated_at` DATETIME DEFAULT NULL,
    `deleted_at` DATETIME DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Seed satuan dasar.
--    IDEMPOTEN: tiap satuan hanya dimasukkan kalau namanya belum ada.
--    Aman dijalankan berulang dan satuan baru (mis. rtg) langsung ikut masuk
--    tanpa menggandakan data lama.
INSERT INTO `units` (`name`,`note`,`created_at`,`updated_at`)
SELECT 'pcs','Satuan dasar / bijian',NOW(),NOW() FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `units` WHERE `name`='pcs');
INSERT INTO `units` (`name`,`note`,`created_at`,`updated_at`)
SELECT 'karton','Kemasan karton',NOW(),NOW() FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `units` WHERE `name`='karton');
INSERT INTO `units` (`name`,`note`,`created_at`,`updated_at`)
SELECT 'slop','Bundel / slop',NOW(),NOW() FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `units` WHERE `name`='slop');
INSERT INTO `units` (`name`,`note`,`created_at`,`updated_at`)
SELECT 'dus','Kemasan dus',NOW(),NOW() FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `units` WHERE `name`='dus');
INSERT INTO `units` (`name`,`note`,`created_at`,`updated_at`)
SELECT 'box','Kemasan box',NOW(),NOW() FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `units` WHERE `name`='box');
INSERT INTO `units` (`name`,`note`,`created_at`,`updated_at`)
SELECT 'pack','Kemasan pack',NOW(),NOW() FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `units` WHERE `name`='pack');
INSERT INTO `units` (`name`,`note`,`created_at`,`updated_at`)
SELECT 'lusin','Isi 12 pcs',NOW(),NOW() FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `units` WHERE `name`='lusin');
INSERT INTO `units` (`name`,`note`,`created_at`,`updated_at`)
SELECT 'rim','Isi 500 lembar',NOW(),NOW() FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `units` WHERE `name`='rim');
INSERT INTO `units` (`name`,`note`,`created_at`,`updated_at`)
SELECT 'sachet','Kemasan sachet',NOW(),NOW() FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `units` WHERE `name`='sachet');
INSERT INTO `units` (`name`,`note`,`created_at`,`updated_at`)
SELECT 'botol','Kemasan botol',NOW(),NOW() FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `units` WHERE `name`='botol');
INSERT INTO `units` (`name`,`note`,`created_at`,`updated_at`)
SELECT 'kaleng','Kemasan kaleng',NOW(),NOW() FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `units` WHERE `name`='kaleng');
INSERT INTO `units` (`name`,`note`,`created_at`,`updated_at`)
SELECT 'kg','Berat kilogram',NOW(),NOW() FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `units` WHERE `name`='kg');
INSERT INTO `units` (`name`,`note`,`created_at`,`updated_at`)
SELECT 'gram','Berat gram',NOW(),NOW() FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `units` WHERE `name`='gram');
INSERT INTO `units` (`name`,`note`,`created_at`,`updated_at`)
SELECT 'liter','Volume liter',NOW(),NOW() FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `units` WHERE `name`='liter');
INSERT INTO `units` (`name`,`note`,`created_at`,`updated_at`)
SELECT 'rtg','Renteng',NOW(),NOW() FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `units` WHERE `name`='rtg');
INSERT INTO `units` (`name`,`note`,`created_at`,`updated_at`)
SELECT 'ml','Volume mililiter',NOW(),NOW() FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `units` WHERE `name`='ml');
