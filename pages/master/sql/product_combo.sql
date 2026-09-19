-- Combine / Racikan Produk
-- Gabungkan beberapa produk jadi satu nama paket (mis: Kopi Susu Gula).
-- Harga total dihitung dari SUM(price * qty) tiap bahan.
-- Catatan: sejak 20-Sep-2026 qty bahan dipakai tetap 1 (tidak ada input qty pada form),
--          tapi kolom qty sengaja dipertahankan agar snapshot harga fleksibel.

CREATE TABLE IF NOT EXISTS product_combos (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    deleted_at INT(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS product_combo_items (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    combo_id INT(11) NOT NULL,
    product_id INT(11) NOT NULL,
    qty INT(11) NOT NULL DEFAULT 1,
    price DECIMAL(10,2) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Contoh query daftar racikan beserta total harganya:
-- SELECT c.id, c.name, c.created_at,
--        CONCAT('Rp ', FORMAT(COALESCE(SUM(ci.price * ci.qty),0), 0, 'id_ID')) AS total
-- FROM product_combos c
-- LEFT JOIN product_combo_items ci ON ci.combo_id = c.id
-- WHERE c.deleted_at IS NULL
-- GROUP BY c.id
-- ORDER BY c.id DESC;