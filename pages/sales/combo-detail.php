<?php
error_reporting(0);
ini_set('display_errors', '0');
include '../../sessions/session.php';

header('Content-Type: application/json');
header('Cache-Control: no-store, no-cache, must-revalidate');
mysqli_report(MYSQLI_REPORT_OFF);

// id boleh "c5" (dari katalog/keranjang) atau "5"
$raw = isset($_GET['id']) ? trim((string)$_GET['id']) : '';
$comboId = (int)preg_replace('/^c/i', '', $raw);

if($comboId <= 0){
    echo json_encode(['status' => 'error', 'message' => 'ID racikan tidak valid']);
    exit;
}

$comboQuery = $conn->prepare("SELECT id, name FROM product_combos WHERE id=? AND deleted_at IS NULL LIMIT 1");
$comboQuery->bind_param("i", $comboId);
$comboQuery->execute();
$comboResult = $comboQuery->get_result();
$combo = $comboResult ? $comboResult->fetch_assoc() : null;

if(!$combo){
    echo json_encode(['status' => 'error', 'message' => 'Racikan tidak ditemukan']);
    exit;
}

$itemQuery = $conn->prepare("
    SELECT ci.qty, ci.price,
           p.id AS product_id, p.name AS product_name,
           p.code, COALESCE(p.unit, '') AS unit, COALESCE(p.photo, '') AS photo
    FROM product_combo_items ci
    INNER JOIN products p ON p.id = ci.product_id
    WHERE ci.combo_id=?
    ORDER BY ci.id ASC
");
$itemQuery->bind_param("i", $comboId);
$itemQuery->execute();
$itemResult = $itemQuery->get_result();

$items = [];
$total = 0;
if($itemResult){
    while($row = $itemResult->fetch_assoc()){
        $row['qty']   = (int)$row['qty'];
        $row['price'] = (float)$row['price'];
        $total += $row['qty'] * $row['price'];
        $items[] = $row;
    }
}

echo json_encode([
    'status' => 'success',
    'combo'  => ['id' => (int)$combo['id'], 'name' => $combo['name']],
    'total'  => $total,
    'items'  => $items,
]);
