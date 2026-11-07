<?php
include '../../sessions/session.php';

header('Content-Type: application/json');

$adjId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if($adjId <= 0){
    echo json_encode(['success' => false, 'message' => 'ID adjustment tidak valid']);
    exit;
}

$q = mysqli_query($conn, "
    SELECT sa.id, sa.product_id, sa.type, sa.location, sa.purchase_item_id, sa.qty, sa.note,
           pi.remaining_qty
    FROM stock_adjustments sa
    LEFT JOIN purchase_items pi ON pi.id = sa.purchase_item_id
    WHERE sa.id = $adjId
");

if(!$q || mysqli_num_rows($q) === 0){
    echo json_encode(['success' => false, 'message' => 'Adjustment tidak ditemukan']);
    exit;
}

$adj = mysqli_fetch_assoc($q);

echo json_encode([
    'success' => true,
    'data' => [
        'id'                => (int)$adj['id'],
        'product_id'        => (int)$adj['product_id'],
        'type'              => $adj['type'],
        'location'          => $adj['location'],
        'purchase_item_id'  => (int)$adj['purchase_item_id'],
        'qty'               => (int)$adj['qty'],
        'note'              => $adj['note'],
        'remaining_qty'     => $adj['remaining_qty'] !== null ? (int)$adj['remaining_qty'] : null
    ]
]);
