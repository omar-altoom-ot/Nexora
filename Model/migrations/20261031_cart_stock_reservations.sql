ALTER TABLE cart_items
    ADD COLUMN stock_reserved_quantity INT UNSIGNED NOT NULL DEFAULT 0
    AFTER quantity;
