-- =====================================================================
-- Migrasi Daftar Belanja: gabung list_purchases/list_purchase_items
-- ke dalam purchases/purchase_items
-- =====================================================================

-- 1. purchases: hapus kolom note (tanggal beli tetap di kolom date)
ALTER TABLE purchases
    DROP COLUMN note;

-- 2. purchase_items:
--    - tambah qty_buy  (qty rencana belanja) setelah qty
--    - tambah unit_buy (satuan rencana belanja) setelah unit
--    - hapus kolom date (tanggal sekarang dari purchases.date)
--    - rename buy_price -> price  (harga beli aktual saat stok masuk)
--    - tambah price_buy (harga rencana dari daftar belanja) setelah price
--    - unit dibuat nullable: baris daftar belanja mengosongkan qty/unit/remaining_qty/price
ALTER TABLE purchase_items
    ADD COLUMN qty_buy INT(11) DEFAULT NULL AFTER qty,
    ADD COLUMN unit_buy VARCHAR(20) DEFAULT NULL AFTER unit,
    DROP COLUMN date,
    CHANGE COLUMN buy_price price DECIMAL(10,2) DEFAULT NULL,
    ADD COLUMN price_buy DECIMAL(10,2) DEFAULT NULL AFTER price;

ALTER TABLE purchase_items
    MODIFY unit VARCHAR(20) DEFAULT NULL;

-- 3. hapus tabel daftar belanja lama
DROP TABLE list_purchase_items;
DROP TABLE list_purchases;