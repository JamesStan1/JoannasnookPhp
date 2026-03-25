-- Payment settings: GCash and Maya numbers shown on the Online Reservation Form
-- Managed by IT Administrator via the IT Dashboard

INSERT IGNORE INTO `settings` (`key`, `value`, `created_at`, `updated_at`)
VALUES
  ('gcash_number', '09659047100', NOW(), NOW()),
  ('maya_number',  '09659047100', NOW(), NOW());
