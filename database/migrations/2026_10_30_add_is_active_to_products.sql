
-- NEXORA: Add Soft Delete support to products
-- Run once on an existing database without is_active.
-- Do not rerun if the column already exists.

ALTER TABLE products
ADD COLUMN is_active TINYINT(1) NOT NULL DEFAULT 1;
