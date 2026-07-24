<?php
include '../../sessions/session.php';

header('Content-Type: application/json');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'ID tidak valid.'
    ]);
    exit;
}

// ==============================
// Ambil data update
// ==============================

$q = mysqli_query($conn, "
    SELECT *
    FROM updates
    WHERE id = '$id'
    LIMIT 1
");

if (!$q) {
    echo json_encode([
        'status' => 'error',
        'message' => mysqli_error($conn)
    ]);
    exit;
}

if (mysqli_num_rows($q) == 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Data tidak ditemukan.'
    ]);
    exit;
}

$update = mysqli_fetch_assoc($q);

// ==============================
// Badge Type
// ==============================

switch ($update['update_type']) {

    case 'major':
        $badge = '<span class="stock-badge stock-danger text-capitalize">Major Update</span>';
        break;

    case 'minor':
        $badge = '<span class="stock-badge stock-success text-capitalize">Minor Update</span>';
        break;

    default:
        $badge = '<span class="stock-badge unit-badge text-capitalize">Patch Update</span>';
        break;
}

// ==============================
// Ambil detail update
// ==============================

$details = [];

$qDetail = mysqli_query($conn, "
    SELECT description
    FROM update_details
    WHERE update_id = '$id'
    ORDER BY id ASC
");

while ($d = mysqli_fetch_assoc($qDetail)) {

    $details[] = [
        'description' => htmlspecialchars($d['description'])
    ];

}

// ==============================
// Return JSON
// ==============================

echo json_encode([
    'status'          => 'success',
    'id'              => $update['id'],
    'update_name'     => htmlspecialchars($update['update_name']),
    'update_version'  => htmlspecialchars($update['update_version']),
    'update_type'     => $update['update_type'],
    'update_date' => date('d F Y | H:i', strtotime($update['update_date'])),
    'badge'           => $badge,
    'details'         => $details
]);