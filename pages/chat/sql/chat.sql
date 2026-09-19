-- Migrasi fitur Chat 1-on-1 (realtime via polling)
-- MySQL 5.6 compatible

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

-- Kolom deleted_at untuk hapus pesan/chat secara realtime (soft delete)
-- Cara baca: pesan dianggap terhapus bila deleted_at IS NOT NULL.
-- Apabila tabel sudah pernah dibuat sebelumnya, jalankan:
-- ALTER TABLE chat_messages ADD COLUMN deleted_at DATETIME DEFAULT NULL AFTER read_at;

-- Kolom kehadiran online (diupdate dari polling)
ALTER TABLE users ADD COLUMN last_seen DATETIME DEFAULT NULL;