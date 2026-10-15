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

$existing = array();
$q = mysqli_query($conn, "SELECT name FROM units WHERE deleted_at IS NULL");
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
    $r         = $rows[$i];
    $rowNo     = $i + 1;
    $name      = trim(isset($r[0]) ? (string)$r[0] : '');
    $note      = trim(isset($r[1]) ? (string)$r[1] : '');

    if ($name === '' && $note === '') {
        continue;
    }
    if ($name !== '' && $name[0] === '#') {
        continue;
    }

    if ($name === '') {
        $rowErrors[] = 'Baris ' . $rowNo . ': nama unit wajib diisi';
        continue;
    }

    $key = strtolower($name);
    if (isset($existing[$key]) || isset($seen[$key])) {
        $duplicated++;
        continue;
    }
    $seen[$key] = true;

    $cname = mysqli_real_escape_string($conn, $name);
    $cnote = mysqli_real_escape_string($conn, $note);

    $ok = mysqli_query($conn, "INSERT INTO units (name, note, created_at, updated_at) VALUES ('$cname', '$cnote', NOW(), NOW())");
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
