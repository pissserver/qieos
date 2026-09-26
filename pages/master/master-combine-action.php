<?php
error_reporting(0);
ini_set('display_errors', '0');
include __DIR__ . '/../../sessions/session.php';

header('Content-Type: application/json');
header('Cache-Control: no-store, no-cache, must-revalidate');
mysqli_report(MYSQLI_REPORT_OFF);

$action = isset($_GET['action']) ? $_GET['action'] : '';

function comboRespond($status, $message, $extra = []){
    echo json_encode(array_merge(['status' => $status, 'message' => $message], $extra));
    exit;
}

// Resolusi bahan valid -> snapshots harga jual terkini (qty selalu 1, tanpa kolom qty dari form)
function comboBuildItems($conn, $pids){
    $items  = [];
    $idList = [];
    foreach($pids as $pid){
        $pid = (int)$pid;
        if($pid <= 0) continue;
        if(in_array($pid, $idList, true)) continue;
        $idList[] = $pid;
        $items[]  = ['product_id' => $pid];
    }
    if(empty($items)) comboRespond('error', 'Pilih minimal satu produk');

    $priceMap = [];
    $idsStr   = implode(',', $idList);
    $r = mysqli_query($conn, "SELECT id, sell_price FROM products WHERE id IN ($idsStr) AND deleted_at IS NULL");
    if($r){
        while($row = mysqli_fetch_assoc($r)){
            $priceMap[(int)$row['id']] = (float)$row['sell_price'];
        }
    }
    if(count($priceMap) !== count($idList)) comboRespond('error', 'Ada produk yang tidak valid atau sudah dihapus');

    foreach($items as $i => $it){
        $items[$i]['qty']   = 1;
        $items[$i]['price'] = $priceMap[$it['product_id']];
    }
    return $items;
}

function comboSaveItems($conn, $comboId, $items){
    foreach($items as $it){
        mysqli_query($conn, "INSERT INTO product_combo_items (combo_id, product_id, qty, price)
                             VALUES ($comboId, {$it['product_id']}, {$it['qty']}, {$it['price']})");
    }
}

// STORE — simpan nama racikan + bahan (produk, tanpa qty), harga snapshot dari sell_price produk
if($action === 'store'){

    $name = trim(isset($_POST['name']) ? $_POST['name'] : '');
    $pids = isset($_POST['product_id']) ? $_POST['product_id'] : [];

    if($name === '') comboRespond('error', 'Nama racikan wajib diisi');

    $items = comboBuildItems($conn, $pids);

    $nameSql = mysqli_real_escape_string($conn, $name);
    $ok = mysqli_query($conn, "INSERT INTO product_combos (name, created_at) VALUES ('$nameSql', NOW())");
    $comboId = $ok ? (int)mysqli_insert_id($conn) : 0;
    if($comboId <= 0) comboRespond('error', 'Gagal menyimpan racikan');

    comboSaveItems($conn, $comboId, $items);

    comboRespond('success', 'Racikan berhasil ditambahkan', ['id' => $comboId]);
}

// EDIT — perbarui nama + bahan racikan yang sudah ada
if($action === 'edit'){

    $id   = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $name = trim(isset($_POST['name']) ? $_POST['name'] : '');
    $pids = isset($_POST['product_id']) ? $_POST['product_id'] : [];

    if($id <= 0) comboRespond('error', 'ID tidak valid');
    if($name === '') comboRespond('error', 'Nama racikan wajib diisi');

    $items = comboBuildItems($conn, $pids);

    $nameSql = mysqli_real_escape_string($conn, $name);
    $ok = mysqli_query($conn, "UPDATE product_combos SET name = '$nameSql' WHERE id = $id AND deleted_at IS NULL");
    if(!$ok || mysqli_affected_rows($conn) < 0) comboRespond('error', 'Racikan tidak ditemukan');

    mysqli_query($conn, "DELETE FROM product_combo_items WHERE combo_id = $id");
    comboSaveItems($conn, $id, $items);

    comboRespond('success', 'Racikan berhasil diperbarui', ['id' => $id]);
}

// DESTROY — soft delete
if($action === 'destroy'){

    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    if($id <= 0) comboRespond('error', 'ID tidak valid');

    if(mysqli_query($conn, "UPDATE product_combos SET deleted_at = UNIX_TIMESTAMP() WHERE id = $id")){
        comboRespond('success', 'Racikan berhasil dihapus');
    }
    comboRespond('error', 'Gagal menghapus racikan');
}

comboRespond('error', 'unknown action');