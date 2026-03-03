-- Add qr_token column to users table for QR-based attendance
ALTER TABLE users ADD COLUMN IF NOT EXISTS qr_token VARCHAR(64) NULL UNIQUE AFTER phone;

-- Backfill existing users with a unique token
UPDATE users SET qr_token = MD5(CONCAT(id, '-', NOW(), '-', RAND())) WHERE qr_token IS NULL;
