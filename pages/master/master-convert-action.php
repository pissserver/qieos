<?php
error_reporting(0);
ini_set('display_errors', '0');
include __DIR__ . '/../../sessions/session.php';

header('Content-Type: application/json');
header('Cache-Control: no-store, no-cache, must-revalidate');
mysqli_report(MYSQLI_REPORT_OFF);

$action = isset($_GET['action']) ? $_GET['action'] : '';

function convRespond($status, $message, $extra = []){
    echo json_encode(array_merge(['status' => $status, 'message' => $message], $extra));
    exit;
}

// Daftar nama satuan aktif -> dipakai untuk memvalidasi unit base & baris
function convActiveUnits($conn){
    $set = [];
    $r = mysqli_query($conn, "SELECT name FROM units WHERE deleted_at IS NULL");
    if($r){
        while($row = mysqli_fetch_assoc($r)){ $set[$row['name']] = true; }
    }
    return $set;
}

// Validasi + bangun baris konversi dari input form (unit[] & qty[])
// Aturan: unit wajib dikenal, tidak boleh sama dengan unit base, qty > 0,
// dan satu satuan tidak boleh muncul dua kali.
function convBuildItems($conn, $base, $validUnits){
    $units = isset($_POST['unit']) ? (array)$_POST['unit'] : [];
    $qtys  = isset($_POST['qty'])  ? (array)$_POST['qty']  : [];

    $rows   = [];
    $seen   = [];
    foreach($units as $i => $un){
        $un = trim((string)$un);
        if($un === '') continue;

        if(!isset($validUnits[$un]))
            convRespond('error', 'Satuan "' . $un . '" tidak dikenal');

        if(strcasecmp($un, $base) === 0)
            convRespond('error', 'Satuan konversi tidak boleh sama dengan unit base');

        if(isset($seen[strtolower($un)]))
            convRespond('error', 'Satuan "' . $un . '" dipilih lebih dari sekali');

        $raw = isset($qtys[$i]) ? trim((string)$qtys[$i]) : '';
        $q   = (float)str_replace(',', '.', $raw);
        if($raw === '' || $q <= 0)
            convRespond('error', 'Jumlah konversi untuk "' . $un . '" harus lebih dari 0');

        $seen[strtolower($un)] = true;
        $rows[] = ['unit' => $un, 'qty' => $q];
    }

    if(empty($rows)) convRespond('error', 'Tambahkan minimal satu konversi');

    return $rows;
}

function convSaveItems($conn, $conversionId, $rows){
    $now = 'NOW()';
    foreach($rows as $r){
        $unit  = mysqli_real_escape_string($conn, $r['unit']);
        $qty   = sprintf('%.4f', (float)$r['qty']);
        mysqli_query($conn, "INSERT INTO unit_conversion_items (conversion_id, unit_name, qty, created_at)
                             VALUES ($conversionId, '$unit', $qty, $now)");
    }
}

// Unit base harus dipilih, dikenal, dan belum dipakai grup lain yang masih aktif
function convResolveBase($conn, $validUnits, $excludeId = 0){
    $base = trim(isset($_POST['base_unit']) ? $_POST['base_unit'] : '');
    if($base === '') convRespond('error', 'Unit base wajib dipilih');
    if(!isset($validUnits[$base])) convRespond('error', 'Unit base tidak dikenal');

    $baseSql = mysqli_real_escape_string($conn, $base);
    $dup = mysqli_query($conn, "SELECT id FROM unit_conversions
                                WHERE deleted_at IS NULL AND LOWER(base_unit) = LOWER('$baseSql')
                                  AND id <> " . (int)$excludeId . " LIMIT 1");
    if($dup && $dup->num_rows > 0)
        convRespond('error', 'Konversi dengan unit base "' . $base . '" sudah ada');

    return $base;
}

// STORE — simpan grup konversi baru
if($action === 'store'){

    $validUnits = convActiveUnits($conn);
    $base  = convResolveBase($conn, $validUnits);
    $rows  = convBuildItems($conn, $base, $validUnits);

    $baseSql = mysqli_real_escape_string($conn, $base);
    $ok = mysqli_query($conn, "INSERT INTO unit_conversions (base_unit, created_at, updated_at)
                               VALUES ('$baseSql', NOW(), NOW())");
    $conversionId = $ok ? (int)mysqli_insert_id($conn) : 0;
    if($conversionId <= 0) convRespond('error', 'Gagal menyimpan konversi');

    convSaveItems($conn, $conversionId, $rows);

    convRespond('success', 'Konversi berhasil ditambahkan', ['id' => $conversionId]);
}

// EDIT — perbarui unit base + baris konversi
if($action === 'edit'){

    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    if($id <= 0) convRespond('error', 'ID tidak valid');

    $validUnits = convActiveUnits($conn);
    $base  = convResolveBase($conn, $validUnits, $id);
    $rows  = convBuildItems($conn, $base, $validUnits);

    $baseSql = mysqli_real_escape_string($conn, $base);
    $ok = mysqli_query($conn, "UPDATE unit_conversions SET base_unit = '$baseSql', updated_at = NOW()
                               WHERE id = $id AND deleted_at IS NULL");
    if(!$ok) convRespond('error', 'Konversi tidak ditemukan');

    mysqli_query($conn, "DELETE FROM unit_conversion_items WHERE conversion_id = $id");
    convSaveItems($conn, $id, $rows);

    convRespond('success', 'Konversi berhasil diperbarui', ['id' => $id]);
}

// DESTROY — soft delete
if($action === 'destroy'){

    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    if($id <= 0) convRespond('error', 'ID tidak valid');

    if(mysqli_query($conn, "UPDATE unit_conversions SET deleted_at = NOW() WHERE id = $id")){
        convRespond('success', 'Konversi berhasil dihapus');
    }
    convRespond('error', 'Gagal menghapus konversi');
}

convRespond('error', 'unknown action');
