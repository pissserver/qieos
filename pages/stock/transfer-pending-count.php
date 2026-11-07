<?php
include '../../sessions/session.php';
header('Content-Type: application/json; charset=utf-8');

if(!in_array($user['role'], ['administrator', 'developer'], true)){
    http_response_code(403);
    echo json_encode(['status' => 'error', 'msg' => 'Akses tidak diizinkan']);
    exit;
}

$query = mysqli_query($conn, "
    SELECT COUNT(*) AS pending_count
    FROM stock_requests
    WHERE status = 'pending'
");

if(!$query){
    http_response_code(500);
    echo json_encode(['status' => 'error', 'msg' => 'Gagal memuat jumlah request transfer pending']);
    exit;
}

$result = mysqli_fetch_assoc($query);
echo json_encode([
    'status' => 'success',
    'count' => (int)$result['pending_count']
]);
