-- Forgot password requests submitted from the login page
CREATE TABLE IF NOT EXISTS forgot_password_requests (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    name         VARCHAR(255)        NOT NULL,
    email        VARCHAR(255)        NOT NULL,
    message      TEXT                NOT NULL,
    status       ENUM('pending','resolved','dismissed') NOT NULL DEFAULT 'pending',
    resolved_by  INT                 NULL,
    resolved_at  DATETIME            NULL,
    created_at   DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_fpr_resolved_by FOREIGN KEY (resolved_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
