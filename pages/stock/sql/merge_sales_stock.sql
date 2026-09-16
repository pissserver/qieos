-- =====================================================================
-- Gabung stock_transfers ke sales_stock (jadi 1 tabel: movement/ledger)
-- =====================================================================
--
-- sales_stock sekarang mencatat SETIAP pergerakan stok sebagai satu baris:
--   - type 'balance'   : saldo awal (saldo lama sebelum migrasi)
--   - type 'transfer'  : stok masuk dari gudang (persetujuan transfer)
--   - type 'sale'      : stok keluar karena penjualan
--   - type 'return'    : stok kembali karena pembatalan pesanan
--
-- Saldo stok kantin saat ini = SUM(qty) GROUP BY product_id.

-- 1. Struktur sales_stock jadi ledger stock movement
ALTER TABLE sales_stock
    ADD COLUMN type VARCHAR(20) NOT NULL DEFAULT 'transfer' AFTER qty,
    ADD COLUMN created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER type;

-- 2. Saldo yang sudah ada dijadikan baris awal (opening balance)
UPDATE sales_stock SET type = 'balance';

-- 3. Kolom updated_at tidak dipakai lagi (baris bersifat immutable)
ALTER TABLE sales_stock DROP COLUMN updated_at;

-- 4. Hapus tabel log lama
DROP TABLE stock_transfers;