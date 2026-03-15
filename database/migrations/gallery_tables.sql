-- Gallery Albums & Photos
-- Run: mysql -u root -p hotel_management < database/migrations/gallery_tables.sql

CREATE TABLE IF NOT EXISTS gallery_albums (
    id          INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    name        VARCHAR(255)     NOT NULL,
    description TEXT,
    cover_photo VARCHAR(255)     DEFAULT NULL,
    created_at  TIMESTAMP        DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS gallery_photos (
    id          INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    album_id    INT UNSIGNED     NOT NULL,
    filename    VARCHAR(255)     NOT NULL,
    caption     VARCHAR(255)     DEFAULT NULL,
    created_at  TIMESTAMP        DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (album_id) REFERENCES gallery_albums(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
