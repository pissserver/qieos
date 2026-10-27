ALTER TABLE purchase_items
    ADD COLUMN supplier_id INT NULL AFTER price_buy,
    ADD COLUMN invoice_no VARCHAR(100) NULL AFTER supplier_id;
