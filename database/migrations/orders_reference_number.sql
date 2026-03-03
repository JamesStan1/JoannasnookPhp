-- Add reference_number column to orders table for GCash/Bank Transfer transactions
ALTER TABLE orders
  ADD COLUMN IF NOT EXISTS reference_number VARCHAR(100) NULL AFTER received_amount;
