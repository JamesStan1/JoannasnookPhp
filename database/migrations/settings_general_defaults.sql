-- Seed default general settings
INSERT INTO settings (`key`, `value`, created_at, updated_at) VALUES
  ('hotel_name',            'Joanna\'s Nook Bed & Breakfast', NOW(), NOW()),
  ('hotel_email',           'joannasnookban@gmail.com',       NOW(), NOW()),
  ('hotel_phone',           '09123456789',                    NOW(), NOW()),
  ('hotel_address',         'Balingasag, Misamis Oriental',   NOW(), NOW()),
  ('default_checkin_time',  '14:00',                          NOW(), NOW()),
  ('default_checkout_time', '12:00',                          NOW(), NOW()),
  ('tax_rate',              '12',                             NOW(), NOW()),
  ('currency',              '₱',                             NOW(), NOW()),
  ('email_notifications',   '1',                              NOW(), NOW()),
  ('push_notifications',    '1',                              NOW(), NOW())
ON DUPLICATE KEY UPDATE value = VALUES(value), updated_at = NOW();
