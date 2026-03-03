-- Add image_url column to rooms table
ALTER TABLE rooms ADD COLUMN IF NOT EXISTS image_url VARCHAR(500) DEFAULT NULL AFTER description;
