-- =============================================================
-- 017 - Restore stok kantin untuk pesanan yang masih 'waiting'
--
-- Sebelumnya stok kantin dipotong saat checkout (pesanan dibuat),
-- padahal pesanan belum dibayar. Sekarang pemotongan hanya terjadi
-- saat pesanan dibayar (order-pay.php), jadi pesanan 'waiting' yang
-- terlanjur memotong stok perlu dikembalikan.
--
-- Migrasi ini meng-insert baris 'return' di sales_stock sebagai offset
-- untuk setiap 'sale' yang tadi dilakukan saat checkout.
--
-- Dijalankan SEKALI (dicatat di tabel schema_migrations) supaya stok
-- tidak dikembalikan berkali-kali.
-- =============================================================

-- 1. Produk biasa (product_id tidak NULL)
INSERT INTO `sales_stock` (product_id, qty, type)
SELECT od.product_id, od.qty, 'return'
FROM `order_details` od
INNER JOIN `orders` o ON o.id = od.order_id
WHERE o.status_payment = 'waiting'
  AND od.product_id IS NOT NULL
  AND od.qty > 0;

-- 2. Racikan (combo): kembalikan stok tiap bahan penyusunnya
INSERT INTO `sales_stock` (product_id, qty, type)
SELECT pci.product_id, GREATEST(1, COALESCE(pci.qty, 1)) * od.qty, 'return'
FROM `order_details` od
INNER JOIN `orders` o ON o.id = od.order_id
INNER JOIN `product_combo_items` pci ON pci.combo_id = od.product_combo_id
WHERE o.status_payment = 'waiting'
  AND od.product_id IS NULL
  AND od.product_combo_id IS NOT NULL
  AND od.qty > 0;
