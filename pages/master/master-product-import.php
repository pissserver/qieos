<?php
include '../../sessions/session.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Metode tidak diizinkan']);
    exit;
}

if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['status' => 'error', 'message' => 'Pilih file Excel terlebih dahulu']);
    exit;
}

$tmp  = $_FILES['file']['tmp_name'];
$orig = $_FILES['file']['name'];
$ext  = strtolower(pathinfo($orig, PATHINFO_EXTENSION));

if (!in_array($ext, array('xlsx', 'xls'))) {
    echo json_encode(['status' => 'error', 'message' => 'Format file harus .xlsx atau .xls']);
    exit;
}

if ($_FILES['file']['size'] > 5 * 1024 * 1024) {
    echo json_encode(['status' => 'error', 'message' => 'Ukuran file maksimal 5MB']);
    exit;
}

require '../../vendor/phpoffice/phpexcel/Classes/PHPExcel/IOFactory.php';

try {
    $reader = PHPExcel_IOFactory::createReaderForFile($tmp);
    $reader->setReadDataOnly(true);
    $obj = $reader->load($tmp);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'File tidak dapat dibaca, pastikan format Excel benar']);
    exit;
}

$rows = $obj->getActiveSheet()->toArray();

// Cari baris header: kolom pertama bertuliskan "code"
$headerRow = -1;
foreach ($rows as $i => $r) {
    if (isset($r[0]) && strtolower(trim((string)$r[0])) === 'code') {
        $headerRow = $i;
        break;
    }
}
if ($headerRow < 0) {
    echo json_encode(['status' => 'error', 'message' => 'Header "code" tidak ditemukan. Pastikan memakai template resmi.']);
    exit;
}

// Kategori yang boleh diimport (harus sama dengan master-product-template.php).
// Kategori "Additional" dibuat manual lewat form tambah produk.
$categories = ['Makanan', 'Minuman', 'Jajanan', 'Pelengkap'];

// Kode produk aktif yang sudah ada -> duplikat dilewati
$existing = array();
$q = mysqli_query($conn, "SELECT code FROM products WHERE deleted_at IS NULL");
if ($q) {
    while ($d = mysqli_fetch_assoc($q)) {
        $existing[strtolower(trim($d['code']))] = true;
    }
}

$inserted   = 0;
$duplicated = 0;
$rowErrors  = array();
$seen       = array();

for ($i = $headerRow + 1; $i < count($rows); $i++) {
    $r         = $rows[$i];
    $rowNo     = $i + 1;
    $code      = trim(isset($r[0]) ? (string)$r[0] : '');
    $name      = trim(isset($r[1]) ? (string)$r[1] : '');
    $category  = trim(isset($r[2]) ? (string)$r[2] : '');
    $priceRaw  = trim(isset($r[3]) ? (string)$r[3] : '');
    $unit      = trim(isset($r[4]) ? (string)$r[4] : '');
    $lowRaw    = trim(isset($r[5]) ? (string)$r[5] : '');
    $lowKanRaw = trim(isset($r[6]) ? (string)$r[6] : '');

    // Baris kosong -> dilewati
    if ($code === '' && $name === '' && $category === '' && $priceRaw === '' && $unit === '' && $lowRaw === '' && $lowKanRaw === '') {
        continue;
    }
    // Baris catatan / contoh (diawali '#') -> dilewati
    if ($code !== '' && $code[0] === '#') {
        continue;
    }

    // ---- Validasi baris ----
    if ($code === '') {
        $rowErrors[] = 'Baris ' . $rowNo . ': kode produk wajib diisi';
        continue;
    }
    if ($name === '') {
        $rowErrors[] = 'Baris ' . $rowNo . ': nama produk wajib diisi';
        continue;
    }

    // Kategori: case-insensitive, disimpan dalam bentuk baku (canonical)
    $catKey = null;
    foreach ($categories as $idx => $catName) {
        if (strtolower($category) === strtolower($catName)) {
            $catKey = $idx;
            break;
        }
    }
    if ($catKey === null) {
        if (strtolower($category) === 'additional') {
            $rowErrors[] = 'Baris ' . $rowNo . ': kategori Additional dibuat manual lewat form, tidak lewat import';
        } else {
            $rowErrors[] = 'Baris ' . $rowNo . ': kategori "' . $category . '" tidak valid (pilih: ' . implode(', ', $categories) . ')';
        }
        continue;
    }
    $category = $categories[$catKey];

    // Harga: wajib numerik >= 0 (0 diperbolehkan, produk gratisan)
    if ($priceRaw === '' || !is_numeric($priceRaw) || (float)$priceRaw < 0) {
        $rowErrors[] = 'Baris ' . $rowNo . ': harga jual wajib diisi angka >= 0';
        continue;
    }
    $priceSql = number_format((float)$priceRaw, 2, '.', '');

    // low_stock & low_stock_kantin: numerik >= 0, default 0 jika kosong
    $lowStock = 0;
    if ($lowRaw !== '') {
        if (!is_numeric($lowRaw) || (float)$lowRaw < 0) {
            $rowErrors[] = 'Baris ' . $rowNo . ': low_stock harus angka >= 0';
            continue;
        }
        $lowStock = max(0, (int)$lowRaw);
    }

    $lowStockKantin = 0;
    if ($lowKanRaw !== '') {
        if (!is_numeric($lowKanRaw) || (float)$lowKanRaw < 0) {
            $rowErrors[] = 'Baris ' . $rowNo . ': low_stock_kantin harus angka >= 0';
            continue;
        }
        $lowStockKantin = max(0, (int)$lowKanRaw);
    }

    // Cek duplikat kode (terhadap data aktif + baris lain di file ini)
    $key = strtolower($code);
    if (isset($existing[$key]) || isset($seen[$key])) {
        $duplicated++;
        continue;
    }
    $seen[$key] = true;

    $ccode      = mysqli_real_escape_string($conn, $code);
    $cname      = mysqli_real_escape_string($conn, $name);
    $ccategory  = mysqli_real_escape_string($conn, $category);
    $cunit      = mysqli_real_escape_string($conn, $unit);
    $unitSql    = $unit === '' ? 'NULL' : "'$cunit'";

    // low_stock & low_stock_kantin per-produk; foto/starred/catalog pakai default
    $ok = mysqli_query($conn, "INSERT INTO products (code, name, category, sell_price, unit, low_stock, low_stock_kantin, created_at) VALUES ('$ccode', '$cname', '$ccategory', $priceSql, $unitSql, $lowStock, $lowStockKantin, NOW())");
    if ($ok) {
        $inserted++;
    } else {
        $rowErrors[] = 'Baris ' . $rowNo . ': gagal menyimpan "' . $name . '"';
    }
}

echo json_encode(array(
    'status'     => 'success',
    'inserted'   => $inserted,
    'duplicated' => $duplicated,
    'errors'     => $rowErrors,
));
exit;