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
$editId    = isset($input['edit_id']) ? (int)$input['edit_id'] : 0;

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

/* ===== Mode edit: muat adjustment lama ===== */
$old = null;
if($editId > 0){
    $oldQ = mysqli_query($conn, "
        SELECT id, location, type, purchase_item_id, qty, created_at
        FROM stock_adjustments
        WHERE id = $editId AND product_id = $productId
    ");
    if(!$oldQ || mysqli_num_rows($oldQ) === 0){
        echo json_encode(['success' => false, 'message' => 'Adjustment lama tidak ditemukan']);
        exit;
    }
    $old = mysqli_fetch_assoc($oldQ);
}

$layerRow = null;

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

    /* Saat edit, efek adjustment lama pada layer yang sama masih terbukukan.
       Batas qty baru dihitung setelah efek lama dibatalkan. */
    $max = (int)$layerRow['remaining_qty'];
    if($old !== null && $old['location'] === 'gudang' && (int)$old['purchase_item_id'] === $layerId){
        $max += ($old['type'] === 'in') ? -(int)$old['qty'] : (int)$old['qty'];
    }
    if($max < 0) $max = 0;

    if($type === 'out' && $qty > $max){
        echo json_encode(['success' => false, 'message' => 'Qty melebihi sisa stok layer (max: ' . $max . ')']);
        exit;
    }
}

$noteEsc = mysqli_real_escape_string($conn, $note);
$userId  = isset($user['id']) ? (int)$user['id'] : 'NULL';

if($editId > 0){
    /* ===== 1. Batalkan efek adjustment lama ===== */
    if($old['location'] === 'gudang' && (int)$old['purchase_item_id'] > 0){
        $oldLayerId = (int)$old['purchase_item_id'];
        $oldQty = (int)$old['qty'];
        mysqli_query($conn, "UPDATE purchase_items SET remaining_qty = remaining_qty " . ($old['type'] === 'in' ? "- $oldQty" : "+ $oldQty") . " WHERE id = $oldLayerId");
    } elseif($old['location'] === 'kantin'){
        /* Hapus baris ledger lama (cocok product, jenis, qty, waktu insert). */
        $oldLedgerQty = ($old['type'] === 'in') ? (int)$old['qty'] : -(int)$old['qty'];
        $oldCreated = mysqli_real_escape_string($conn, $old['created_at']);
        mysqli_query($conn, "
            DELETE FROM sales_stock
            WHERE product_id = $productId AND type = 'adjustment'
              AND qty = $oldLedgerQty AND created_at = '$oldCreated'
            ORDER BY id DESC LIMIT 1
        ");
    }

    /* ===== 2. Update record adjustment ===== */
    $layerSql = ($location === 'gudang') ? "$layerId" : "NULL";
    mysqli_query($conn, "
        UPDATE stock_adjustments
        SET location = '$location', type = '$type', purchase_item_id = $layerSql,
            qty = $qty, note = '$noteEsc', user_id = $userId
        WHERE id = $editId
    ") or die(json_encode(['success' => false, 'message' => 'Gagal update adjustment']));

} else {
    /* ===== Mode tambah: catat ke tabel history adjustment ===== */
    $layerSql = ($location === 'gudang') ? "$layerId" : "NULL";
    mysqli_query($conn, "
        INSERT INTO stock_adjustments (product_id, location, type, purchase_item_id, qty, note, user_id, created_at)
        VALUES ($productId, '$location', '$type', $layerSql, $qty, '$noteEsc', $userId, NOW())
    ") or die(json_encode(['success' => false, 'message' => 'Gagal menyimpan adjustment']));
}

/* ===== 3. Terapkan efek baru ke stok ===== */
if($location === 'gudang'){
    $remaining  = (int)$layerRow['remaining_qty'];
    $newRemain  = $type === 'in' ? $remaining + $qty : $remaining - $qty;
    mysqli_query($conn, "UPDATE purchase_items SET remaining_qty = $newRemain WHERE id = $layerId");
} else {
    /* Kantin: ledger sales_stock supaya saldo kantin ikut berubah.
       qty positif = penambahan, negatif = pengurangan.
       Saat edit, waktu baris dipertahankan supaya laporan periode tetap cocok. */
    $ledgerQty = $type === 'in' ? $qty : -$qty;
    $createdAt = ($editId > 0) ? "'" . mysqli_real_escape_string($conn, $old['created_at']) . "'" : "NOW()";
    mysqli_query($conn, "
        INSERT INTO sales_stock (product_id, qty, type, note, created_at)
        VALUES ($productId, $ledgerQty, 'adjustment', '$noteEsc', $createdAt)
    ");
}

echo json_encode(['success' => true]);
