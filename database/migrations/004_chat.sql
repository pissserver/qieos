-- =============================================================
-- 004 - Fitur Chat 1-on-1 (polling)
--   * chat_messages  : tabel pesan
--   * users.last_seen: kolom kehadiran online
-- MySQL 5.6 compatible, aman diulang.
-- =============================================================

-- 1. Tabel chat_messages
CREATE TABLE IF NOT EXISTS chat_messages (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    message TEXT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    read_at DATETIME DEFAULT NULL,
    deleted_at DATETIME DEFAULT NULL,
    PRIMARY KEY (id),
    KEY idx_sender (sender_id, id),
    KEY idx_receiver (receiver_id, id),
    KEY idx_receiver_read (receiver_id, read_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Kolom last_seen pada users (kehadiran online)
SET @exists := (SELECT COUNT(*) FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'last_seen');
SET @sql := IF(@exists = 0, 'ALTER TABLE users ADD COLUMN last_seen DATETIME DEFAULT NULL', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;