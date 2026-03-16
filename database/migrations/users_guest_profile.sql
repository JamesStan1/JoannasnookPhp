-- Add address and nationality columns to users table for returning guest autofill
ALTER TABLE users
  ADD COLUMN IF NOT EXISTS address VARCHAR(255) NULL DEFAULT NULL AFTER phone,
  ADD COLUMN IF NOT EXISTS nationality VARCHAR(100) NULL DEFAULT NULL AFTER address;
