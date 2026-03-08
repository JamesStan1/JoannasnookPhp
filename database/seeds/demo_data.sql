-- Sample data for Hotel Management System

-- Insert sample users
INSERT INTO users (name, email, password, phone, role, active, created_at, updated_at) VALUES
('Admin User', 'admin@hotel.com', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/LLm', '+1234567890', 'admin', 1, NOW(), NOW()),
('Manager User', 'manager@hotel.com', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/LLm', '+1234567891', 'manager', 1, NOW(), NOW()),
('Front Desk', 'frontdesk@hotel.com', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/LLm', '+1234567892', 'front_desk', 1, NOW(), NOW()),
('Housekeeping Staff', 'housekeeping@hotel.com', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/LLm', '+1234567893', 'housekeeping', 1, NOW(), NOW()),
('Chef', 'chef@hotel.com', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/LLm', '+1234567894', 'chef', 1, NOW(), NOW()),
('Accountant', 'accountant@hotel.com', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/LLm', '+1234567895', 'accountant', 1, NOW(), NOW()),
('John Doe', 'john@email.com', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/LLm', '+1234567896', 'guest', 1, NOW(), NOW()),
('Jane Smith', 'jane@email.com', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/LLm', '+1234567897', 'guest', 1, NOW(), NOW());

-- Insert sample rooms
INSERT INTO rooms (room_number, type, price, capacity, status, created_at, updated_at) VALUES
('101', 'Single', 80.00, 1, 'available', NOW(), NOW()),
('102', 'Single', 80.00, 1, 'available', NOW(), NOW()),
('103', 'Double', 120.00, 2, 'available', NOW(), NOW()),
('104', 'Double', 120.00, 2, 'available', NOW(), NOW()),
('105', 'Suite', 200.00, 4, 'available', NOW(), NOW()),
('106', 'Suite', 200.00, 4, 'available', NOW(), NOW()),
('201', 'Double', 120.00, 2, 'available', NOW(), NOW()),
('202', 'Double', 120.00, 2, 'available', NOW(), NOW()),
('203', 'Suite', 200.00, 4, 'available', NOW(), NOW()),
('204', 'Suite', 200.00, 4, 'available', NOW(), NOW());

-- Insert sample menu items
INSERT INTO menu_items (name, category_id, description, price, active, created_at) VALUES
('Grilled Salmon', 1, 'Fresh grilled salmon with herbs', 25.00, 1, NOW()),
('Ribeye Steak', 1, 'Premium ribeye steak', 35.00, 1, NOW()),
('Pasta Carbonara', 1, 'Classic Italian pasta', 18.00, 1, NOW()),
('Chicken Curry', 1, 'Spiced chicken curry with rice', 22.00, 1, NOW()),
('Caesar Salad', 2, 'Fresh romaine with parmesan', 12.00, 1, NOW()),
('Greek Salad', 2, 'Tomato, cucumber, olives', 11.00, 1, NOW()),
('Espresso', 3, 'Strong Italian espresso', 3.50, 1, NOW()),
('Cappuccino', 3, 'Espresso with steamed milk', 4.50, 1, NOW()),
('Chocolate Cake', 4, 'Rich chocolate dessert', 8.00, 1, NOW()),
('Cheesecake', 4, 'New York style cheesecake', 9.00, 1, NOW());

-- Insert sample inventory items
INSERT INTO inventory (name, category, current_stock, unit_price, minimum_stock, supplier, created_at) VALUES
('Salmon', 'Seafood', 25, 15.00, 5, 'Fresh Fish Co', NOW()),
('Ribeye Steak', 'Meat', 40, 12.00, 10, 'Premium Meats', NOW()),
('Olive Oil', 'Oils', 20, 8.00, 5, 'Italian Imports', NOW()),
('Flour', 'Baking', 50, 2.00, 20, 'Quality Grains', NOW()),
('Sugar', 'Baking', 45, 1.50, 15, 'Quality Grains', NOW()),
('Eggs', 'Dairy', 100, 3.00, 50, 'Local Farm', NOW()),
('Milk', 'Dairy', 60, 2.50, 20, 'Local Farm', NOW()),
('Coffee Beans', 'Beverages', 30, 10.00, 5, 'Coffee Traders', NOW());

-- Insert sample settings
INSERT INTO settings (`key`, value, created_at, updated_at) VALUES
('hotel_name', 'Joanna\'s Nook Bed & Breakfast', NOW(), NOW()),
('hotel_phone', '09123456789', NOW(), NOW()),
('hotel_email', 'joannasnookban@gmail.com', NOW(), NOW()),
('hotel_address', 'Balingasag, Misamis Oriental', NOW(), NOW()),
('currency', '₱', NOW(), NOW()),
('tax_rate', '12', NOW(), NOW()),
('check_in_time', '14:00', NOW(), NOW()),
('check_out_time', '12:00', NOW(), NOW())
ON DUPLICATE KEY UPDATE value = VALUES(value), updated_at = NOW();

-- Password hash is: password123 (for demo purposes)
