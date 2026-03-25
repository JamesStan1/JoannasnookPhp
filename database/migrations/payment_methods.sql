-- Payment Methods: dynamic, IT-managed list of payment options shown on the
-- Online Reservation Form and the Services page.

CREATE TABLE IF NOT EXISTS `payment_methods` (
  `id`             INT          UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name`           VARCHAR(100) NOT NULL,
  `account_name`   VARCHAR(100) NOT NULL DEFAULT '',
  `account_number` VARCHAR(100) NOT NULL DEFAULT '',
  `instructions`   TEXT,
  `icon`           VARCHAR(10)  NOT NULL DEFAULT '💳',
  `is_active`      TINYINT(1)   NOT NULL DEFAULT 1,
  `sort_order`     INT          NOT NULL DEFAULT 0,
  `created_at`     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed default GCash and Maya entries
-- INSERT IGNORE skips if a row with that id already exists.
INSERT IGNORE INTO `payment_methods` (`id`, `name`, `account_name`, `account_number`, `instructions`, `icon`, `is_active`, `sort_order`)
VALUES
  (1, 'GCash', 'Joanna\'s Nook', '', 'Send to our GCash number and upload the screenshot as proof.', '📱', 1, 1),
  (2, 'Maya',  'Joanna\'s Nook', '', 'Send to our Maya number and upload the screenshot as proof.',  '💳', 1, 2);

-- Backfill account numbers from existing settings if present
UPDATE `payment_methods` pm
  JOIN `settings` s ON s.`key` = 'gcash_number'
SET pm.`account_number` = s.`value`
WHERE pm.`id` = 1 AND pm.`account_number` = '';

UPDATE `payment_methods` pm
  JOIN `settings` s ON s.`key` = 'maya_number'
SET pm.`account_number` = s.`value`
WHERE pm.`id` = 2 AND pm.`account_number` = '';
