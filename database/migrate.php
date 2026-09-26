<?php
/**
 * QIEOS Database Migrator
 * ========================
 * Menjalankan semua file migrasi SQL di folder database/migrations
 * secara berurutan dan mencatatnya di tabel `schema_migrations`.
 * File yang sudah pernah dijalankan akan dilewati (skip).
 *
 * Cara pakai (dari root project):
 *   php database/migrate.php
 *   atau double-click database/migrate.bat
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

$host   = 'localhost';
$user   = 'root';
$pass   = '';
$dbname = 'db_kantin';
$dir    = __DIR__ . '/migrations';

echo "=== QIEOS Database Migrator ===\n\n";

// 1. Koneksi (tanpa database dulu, biar bisa dibuatkan saat belum ada)
$conn = @new mysqli($host, $user, $pass);
if ($conn->connect_error) {
    exit("GAGAL koneksi ke MySQL: " . $conn->connect_error . "\n");
}

if (!$conn->query("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci")) {
    exit("GAGAL membuat database `$dbname`: " . $conn->error . "\n");
}
$conn->select_db($dbname);

// 2. Tabel log migrasi
// CATATAN: nama file dibatasi 191 karakter (utf8mb4) karena
// batas index 767 byte pada InnoDB / MySQL 5.6.
if (!$conn->query("CREATE TABLE IF NOT EXISTS schema_migrations (
    name VARCHAR(191) NOT NULL,
    applied_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4")) {
    exit("GAGAL membuat tabel schema_migrations: " . $conn->error . "\n");
}

// 3. Ambil daftar migrasi yang sudah diterapkan
$applied = [];
$r = $conn->query("SELECT name FROM schema_migrations");
if ($r) while ($row = $r->fetch_assoc()) $applied[$row['name']] = true;

// 4. Scan file *.sql di folder migrations (urut sesuai nama)
$files = glob($dir . '/*.sql');
sort($files);

if (empty($files)) {
    echo "Tidak ada file migrasi di: $dir\n";
    exit(0);
}

$run   = 0;
$skip  = 0;
$failed = 0;

foreach ($files as $file) {
    $name = basename($file);

    if (isset($applied[$name])) {
        echo "  [SKIP] $name (sudah diterapkan)\n";
        $skip++;
        continue;
    }

    echo "  [RUN ] $name ... ";
    $sql = file_get_contents($file);

    if (!$conn->multi_query($sql)) {
        echo "GAGAL\n       " . $conn->error . "\n";
        $failed++;
        continue;
    }

    do {
        if ($res = $conn->store_result()) $res->free();
        if ($conn->error) break;
    } while ($conn->more_results() && $conn->next_result());

    if ($conn->error) {
        echo "GAGAL\n       " . $conn->error . "\n";
        $failed++;
        // buang sisa result kalau ada
        while ($conn->more_results() && $conn->next_result()) {
            if ($res = $conn->store_result()) $res->free();
        }
        continue;
    }

    $stmt = $conn->prepare("INSERT INTO schema_migrations (name) VALUES (?)");
    $stmt->bind_param('s', $name);
    $stmt->execute();
    $stmt->close();

    echo "OK\n";
    $run++;
}

echo "\nSelesai.\n";
echo "  Dijalankan : $run file\n";
echo "  Dilewati   : $skip file\n";
echo "  Gagal      : $failed file\n";

if ($failed > 0) {
    exit(1);
}