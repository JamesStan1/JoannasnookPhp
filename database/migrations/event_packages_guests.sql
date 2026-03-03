-- Add max_guests and max_per_dish columns to event_packages table
ALTER TABLE event_packages ADD COLUMN IF NOT EXISTS max_guests INT DEFAULT 0 AFTER price;
ALTER TABLE event_packages ADD COLUMN IF NOT EXISTS max_per_dish INT DEFAULT 0 AFTER max_guests;
