-- Add all missing columns to pending_reservations required by PendingReservationController::create()
ALTER TABLE pending_reservations
  ADD COLUMN IF NOT EXISTS reference_number  VARCHAR(100)    NULL AFTER id,
  ADD COLUMN IF NOT EXISTS guest_address     VARCHAR(255)    NULL AFTER guest_contact,
  ADD COLUMN IF NOT EXISTS guest_country     VARCHAR(100)    NULL AFTER guest_address,
  ADD COLUMN IF NOT EXISTS event_name        VARCHAR(255)    NULL AFTER number_of_guests,
  ADD COLUMN IF NOT EXISTS event_type        VARCHAR(100)    NULL AFTER event_name,
  ADD COLUMN IF NOT EXISTS buffet_set        VARCHAR(100)    NULL AFTER event_package_id,
  ADD COLUMN IF NOT EXISTS supervisor        VARCHAR(255)    NULL AFTER buffet_set,
  ADD COLUMN IF NOT EXISTS selected_foods    TEXT            NULL AFTER supervisor,
  ADD COLUMN IF NOT EXISTS remarks           TEXT            NULL AFTER special_requests,
  ADD COLUMN IF NOT EXISTS estimated_total   DECIMAL(10,2)   NOT NULL DEFAULT 0 AFTER rejection_reason;
