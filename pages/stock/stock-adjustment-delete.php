<?php
include '../../sessions/session.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$adjId = isset($input['id']) ? (int)$input['id'] : 0;

if($adjId <= 0){
    echo json_encode(['success' => false, 'message' => 'ID adjustment tidak valid']);
    exit;
}

$q = mysqli_query($conn, "
    SELECT sa.id, sa.product_id, sa.location, sa.type, sa.purchase_item_id, sa.qty, sa.created_at
    FROM stock_adjustments sa
    WHERE sa.id = $adjId
");

if(!$q || mysqli_num_rows($q) === 0){
    echo json_encode(['success' => false, 'message' => 'Adjustment tidak ditemukan']);
    exit;
}

$adj = mysqli_fetch_assoc($q);
$productId = (int)$adj['product_id'];
$type = $adj['type'];
$qty = (int)$adj['qty'];
$layerId = (int)$adj['purchase_item_id'];

/* Batalkan efek stok */
if($adj['location'] === 'gudang' && $layerId > 0){
    mysqli_query($conn, "UPDATE purchase_items SET remaining_qty = remaining_qty " . ($type === 'in' ? "- $qty" : "+ $qty") . " WHERE id = $layerId");
} elseif($adj['location'] === 'kantin'){
    /* Hapus baris ledger lama (cocok product, jenis, qty, waktu insert). */
    $ledgerQty = ($type === 'in') ? $qty : -$qty;
    $created = mysqli_real_escape_string($conn, $adj['created_at']);
    mysqli_query($conn, "
        DELETE FROM sales_stock
        WHERE product_id = $productId AND type = 'adjustment'
          AND qty = $ledgerQty AND created_at = '$created'
        ORDER BY id DESC LIMIT 1
    ");
}

mysqli_query($conn, "DELETE FROM stock_adjustments WHERE id = $adjId");

echo json_encode(['success' => true]);
