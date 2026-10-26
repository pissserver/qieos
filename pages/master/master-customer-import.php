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

// Cari baris header: kolom pertama bertuliskan "name"
$headerRow = -1;
foreach ($rows as $i => $r) {
    if (isset($r[0]) && strtolower(trim((string)$r[0])) === 'name') {
        $headerRow = $i;
        break;
    }
}
if ($headerRow < 0) {
    echo json_encode(['status' => 'error', 'message' => 'Header "name" tidak ditemukan. Pastikan memakai template resmi.']);
    exit;
}

// Nama customer aktif yang sudah ada -> duplikat dilewati
$existing = array();
$q = mysqli_query($conn, "SELECT name FROM customers WHERE deleted_at IS NULL");
if ($q) {
    while ($d = mysqli_fetch_assoc($q)) {
        $existing[strtolower(trim($d['name']))] = true;
    }
}

$inserted   = 0;
$duplicated = 0;
$rowErrors  = array();
$seen       = array();

for ($i = $headerRow + 1; $i < count($rows); $i++) {
    $r      = $rows[$i];
    $rowNo  = $i + 1;
    $name   = trim(isset($r[0]) ? (string)$r[0] : '');
    $phone  = trim(isset($r[1]) ? (string)$r[1] : '');

    if ($name === '') {
        continue;
    }
    // Baris catatan / contoh (diawali '#') -> dilewati
    if ($name[0] === '#') {
        continue;
    }

    // Normalisasi No. Telepon: buang spasi, tanda hubung, kurung; sisakan + dan digit
    $phone = preg_replace('/[^0-9+]/', '', $phone);

    $key = strtolower($name);
    if (isset($existing[$key]) || isset($seen[$key])) {
        $duplicated++;
        continue;
    }
    $seen[$key] = true;

    $cname  = mysqli_real_escape_string($conn, $name);
    $cphone = mysqli_real_escape_string($conn, $phone);
    $ok = mysqli_query($conn, "INSERT INTO customers (name, phone, created_at, updated_at) VALUES ('$cname', '$cphone', NOW(), NOW())");
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