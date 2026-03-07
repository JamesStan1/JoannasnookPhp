-- Create chef_orders table (used by POS checkout and Chef Dashboard)
CREATE TABLE IF NOT EXISTS chef_orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    pos_order_id INT NULL,
    invoice_id VARCHAR(100) NULL,
    item_name VARCHAR(255) NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    notes TEXT NULL,
    status ENUM('pending', 'preparing', 'ready', 'served') NOT NULL DEFAULT 'pending',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (pos_order_id) REFERENCES orders(id) ON DELETE SET NULL,
    INDEX idx_invoice_id (invoice_id),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
);
