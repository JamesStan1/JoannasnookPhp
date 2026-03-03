-- Add avatar column to users table for persistent profile picture storage
ALTER TABLE users ADD COLUMN IF NOT EXISTS avatar VARCHAR(500) DEFAULT NULL AFTER updated_at;
