-- Migrasi order_details untuk mendukung Racikan / Combine Produk.
--
-- 1) product_combo_id  : id combo (product_combos.id) saat item adalah racikan,
--                        NULL untuk produk biasa.
-- 2) name              : nama racikan saat item adalah racikan,
--                        NULL untuk produk biasa (nama diambil dari tabel products).
--
-- Aturan penyimpanan:
--   * Produk biasa -> product_id = <id produk>, product_combo_id = NULL, name = NULL
--   * Racikan       -> product_id = NULL, product_combo_id = <id combo>, name = <nama racikan>
--
-- Jika sebelumnya pernah menyimpan combo dengan product_id = 0 (skema lama),
-- migrasi sementara boleh dilakukan berdasarkan nama:
--   UPDATE order_details od
--   LEFT JOIN product_combos pc ON pc.name = od.name
--   SET od.product_combo_id = pc.id
--   WHERE od.product_id = 0 AND pc.id IS NOT NULL;

ALTER TABLE order_details
    ADD COLUMN product_combo_id INT(11) NULL AFTER product_id,
    ADD COLUMN name VARCHAR(255) NULL AFTER product_combo_id;