-- Seed default general settings
INSERT INTO settings (`key`, `value`, created_at, updated_at) VALUES
  ('hotel_name',            'SRCB Hotel & Café',  NOW(), NOW()),
  ('hotel_email',           'info@srcbhotel.com', NOW(), NOW()),
  ('hotel_phone',           '09000000000',         NOW(), NOW()),
  ('hotel_address',         'Brgy. Sample, City',  NOW(), NOW()),
  ('default_checkin_time',  '14:00',               NOW(), NOW()),
  ('default_checkout_time', '12:00',               NOW(), NOW()),
  ('tax_rate',              '12',                  NOW(), NOW()),
  ('currency',              '₱',                  NOW(), NOW()),
  ('email_notifications',   '1',                   NOW(), NOW()),
  ('push_notifications',    '1',                   NOW(), NOW())
ON DUPLICATE KEY UPDATE updated_at = updated_at;
