-- Add missing columns to orders table (required by POSController and analytics queries)
ALTER TABLE orders
  ADD COLUMN IF NOT EXISTS cashier_id INT NULL AFTER id,
  ADD COLUMN IF NOT EXISTS customer_name VARCHAR(255) NULL AFTER cashier_id,
  ADD COLUMN IF NOT EXISTS payment_method VARCHAR(50) NULL AFTER order_type,
  ADD COLUMN IF NOT EXISTS received_amount DECIMAL(10,2) NULL AFTER payment_method,
  ADD COLUMN IF NOT EXISTS discount_type VARCHAR(50) NULL AFTER received_amount,
  ADD COLUMN IF NOT EXISTS charge_to_room TINYINT(1) NOT NULL DEFAULT 0 AFTER discount_type,
  ADD COLUMN IF NOT EXISTS room_id INT NULL AFTER charge_to_room,
  ADD COLUMN IF NOT EXISTS room_number VARCHAR(50) NULL AFTER room_id,
  ADD COLUMN IF NOT EXISTS invoice_id VARCHAR(100) NULL AFTER room_number;

-- Add missing columns to reservations table
ALTER TABLE reservations
  ADD COLUMN IF NOT EXISTS payment_option VARCHAR(50) NOT NULL DEFAULT 'full_payment' AFTER down_payment,
  ADD COLUMN IF NOT EXISTS reference_number VARCHAR(100) NULL AFTER payment_option,
  ADD COLUMN IF NOT EXISTS remarks TEXT NULL AFTER special_requests;

-- Add missing columns to pending_reservations table (required by approve/reject endpoints)
ALTER TABLE pending_reservations
  ADD COLUMN IF NOT EXISTS payment_option VARCHAR(50) NULL AFTER status,
  ADD COLUMN IF NOT EXISTS down_payment DECIMAL(10,2) DEFAULT 0 AFTER payment_option,
  ADD COLUMN IF NOT EXISTS approved_by INT NULL AFTER down_payment,
  ADD COLUMN IF NOT EXISTS approved_at DATETIME NULL AFTER approved_by,
  ADD COLUMN IF NOT EXISTS reservation_id INT NULL AFTER approved_at,
  ADD COLUMN IF NOT EXISTS rejection_reason TEXT NULL AFTER reservation_id,
  ADD COLUMN IF NOT EXISTS rejected_at DATETIME NULL AFTER rejection_reason;
