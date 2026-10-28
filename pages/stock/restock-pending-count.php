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
    FROM (
        SELECT p.id
        FROM purchases p
        JOIN purchase_items pi
            ON pi.purchase_id = p.id
            AND pi.deleted_at IS NULL
            AND pi.qty_buy IS NOT NULL
        WHERE p.deleted_at IS NULL
        GROUP BY p.id
        HAVING COUNT(pi.id) > 0
           AND MAX(CASE WHEN pi.qty_stock IS NOT NULL THEN 1 ELSE 0 END) = 0
    ) AS pending_purchases
");

if(!$query){
    http_response_code(500);
    echo json_encode(['status' => 'error', 'msg' => 'Gagal memuat jumlah belanja yang belum selesai']);
    exit;
}

$result = mysqli_fetch_assoc($query);
echo json_encode([
    'status' => 'success',
    'count' => (int)$result['pending_count']
]);
