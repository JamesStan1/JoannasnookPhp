-- Add missing event_id column to bills table
ALTER TABLE bills
    ADD COLUMN event_id INT NULL AFTER reservation_id,
    ADD CONSTRAINT fk_bills_event_id FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE SET NULL,
    ADD INDEX idx_bills_event_id (event_id);
