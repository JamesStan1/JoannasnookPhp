-- Add soft-delete support to rooms table
ALTER TABLE rooms
    ADD COLUMN IF NOT EXISTS deleted_at DATETIME NULL DEFAULT NULL AFTER updated_at,
    ADD INDEX IF NOT EXISTS idx_rooms_deleted_at (deleted_at);
