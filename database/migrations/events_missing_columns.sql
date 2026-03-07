-- Add missing columns to events table
ALTER TABLE events
  ADD COLUMN IF NOT EXISTS event_name VARCHAR(255) NULL AFTER id,
  ADD COLUMN IF NOT EXISTS client_address VARCHAR(255) NULL AFTER client_phone,
  ADD COLUMN IF NOT EXISTS number_of_guests INT NOT NULL DEFAULT 1 AFTER pax,
  ADD COLUMN IF NOT EXISTS additional_guests INT NOT NULL DEFAULT 0 AFTER number_of_guests,
  ADD COLUMN IF NOT EXISTS package_set VARCHAR(255) NULL AFTER package_id,
  ADD COLUMN IF NOT EXISTS price_per_head DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER package_set,
  ADD COLUMN IF NOT EXISTS payment_option VARCHAR(50) NULL AFTER payment_status,
  ADD COLUMN IF NOT EXISTS payment_method VARCHAR(100) NULL AFTER payment_option,
  ADD COLUMN IF NOT EXISTS payment_ref VARCHAR(255) NULL AFTER payment_method,
  ADD COLUMN IF NOT EXISTS online_payment_type VARCHAR(50) NULL AFTER payment_ref,
  ADD COLUMN IF NOT EXISTS supervisor_id INT NULL AFTER online_payment_type,
  ADD COLUMN IF NOT EXISTS booked_by VARCHAR(255) NULL AFTER supervisor_id,
  ADD COLUMN IF NOT EXISTS source VARCHAR(50) NULL DEFAULT 'walk_in' AFTER booked_by,
  ADD COLUMN IF NOT EXISTS remarks TEXT NULL AFTER notes,
  ADD COLUMN IF NOT EXISTS created_by INT NULL AFTER remarks;
