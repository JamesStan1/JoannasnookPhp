-- Add missing columns to inventory table for full functionality
ALTER TABLE inventory
  ADD COLUMN IF NOT EXISTS unit VARCHAR(50) NOT NULL DEFAULT 'piece',
  ADD COLUMN IF NOT EXISTS on_delivery TINYINT(1) NOT NULL DEFAULT 0,
  ADD COLUMN IF NOT EXISTS deleted_at DATETIME NULL DEFAULT NULL;

-- Add index for soft-delete queries
ALTER TABLE inventory ADD INDEX IF NOT EXISTS idx_deleted_at (deleted_at);
