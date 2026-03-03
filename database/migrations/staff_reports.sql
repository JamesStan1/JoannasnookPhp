-- Staff Reports / Complaints Table
CREATE TABLE IF NOT EXISTS staff_reports (
    id INT PRIMARY KEY AUTO_INCREMENT,
    reporter_id INT NOT NULL,
    category ENUM('complaint', 'suggestion', 'incident', 'harassment', 'other') NOT NULL,
    subject VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    is_anonymous TINYINT DEFAULT 0,
    status ENUM('pending', 'in_review', 'resolved', 'dismissed') DEFAULT 'pending',
    admin_response TEXT,
    responded_by INT,
    responded_at DATETIME,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (reporter_id) REFERENCES users(id),
    FOREIGN KEY (responded_by) REFERENCES users(id),
    INDEX idx_reporter_id (reporter_id),
    INDEX idx_status (status),
    INDEX idx_category (category),
    INDEX idx_created_at (created_at)
);
