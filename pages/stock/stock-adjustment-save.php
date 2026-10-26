<?php
include '../../sessions/session.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

$productId = isset($input['product_id']) ? (int)$input['product_id'] : 0;
$type      = isset($input['type']) ? $input['type'] : '';
$location  = isset($input['location']) ? $input['location'] : '';
$layerId   = isset($input['purchase_item_id']) ? (int)$input['purchase_item_id'] : 0;
$qty       = isset($input['qty']) ? (int)$input['qty'] : 0;
$note      = isset($input['note']) ? trim($input['note']) : '';

if($productId <= 0 || !in_array($type, ['in', 'out']) || !in_array($location, ['gudang', 'kantin']) || $qty <= 0){
    echo json_encode(['success' => false, 'message' => 'Data tidak valid']);
    exit;
}

/* Catatan wajib untuk adjustment stok kantin (tidak ada form pembelian). */
if($location === 'kantin' && $note === ''){
    echo json_encode(['success' => false, 'message' => 'Catatan wajib diisi untuk adjustment stok kantin']);
    exit;
}

/* Produk harus ada */
$prod = mysqli_query($conn, "SELECT id, unit FROM products WHERE id = $productId");
if(!$prod || mysqli_num_rows($prod) === 0){
    echo json_encode(['success' => false, 'message' => 'Produk tidak ditemukan']);
    exit;
}
$p = mysqli_fetch_assoc($prod);

$layerRow = null;
$layerForm = '';

if($location === 'gudang'){
    if($layerId <= 0){
        echo json_encode(['success' => false, 'message' => 'Layer FIFO belum dipilih']);
        exit;
    }

    $check = mysqli_query($conn, "
        SELECT pi.id, pi.remaining_qty, p.form
        FROM purchase_items pi
        LEFT JOIN purchases p ON p.id = pi.purchase_id
        WHERE pi.id = $layerId AND pi.product_id = $productId AND pi.deleted_at IS NULL
    ");
    if(!$check || mysqli_num_rows($check) === 0){
        echo json_encode(['success' => false, 'message' => 'Layer FIFO tidak ditemukan']);
        exit;
    }
    $layerRow = mysqli_fetch_assoc($check);
    $layerForm = $layerRow['form'] ? $layerRow['form'] : '';

    if($type === 'out' && $qty > (int)$layerRow['remaining_qty']){
        echo json_encode(['success' => false, 'message' => 'Qty melebihi sisa stok layer (max: ' . (int)$layerRow['remaining_qty'] . ')']);
        exit;
    }
}

$noteEsc = mysqli_real_escape_string($conn, $note);
$userId  = isset($user['id']) ? (int)$user['id'] : 'NULL';

/* ===== 1. Catat ke tabel history adjustment ===== */
$layerSql = ($location === 'gudang') ? "$layerId" : "NULL";
mysqli_query($conn, "
    INSERT INTO stock_adjustments (product_id, location, type, purchase_item_id, qty, note, user_id, created_at)
    VALUES ($productId, '$location', '$type', $layerSql, $qty, '$noteEsc', $userId, NOW())
") or die(json_encode(['success' => false, 'message' => 'Gagal menyimpan adjustment']));

/* ===== 2. Terapkan efek ke stok ===== */
if($location === 'gudang'){
    $remaining  = (int)$layerRow['remaining_qty'];
    $newRemain  = $type === 'in' ? $remaining + $qty : $remaining - $qty;
    mysqli_query($conn, "UPDATE purchase_items SET remaining_qty = $newRemain WHERE id = $layerId");
} else {
    /* Kantin: masuk ledger sales_stock supaya saldo kantin ikut berubah.
       qty positif = penambahan, negatif = pengurangan. */
    $ledgerQty = $type === 'in' ? $qty : -$qty;
    mysqli_query($conn, "
        INSERT INTO sales_stock (product_id, qty, type, note, created_at)
        VALUES ($productId, $ledgerQty, 'adjustment', '$noteEsc', NOW())
    ");
}

echo json_encode(['success' => true]);
