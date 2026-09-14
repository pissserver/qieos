<?php
error_reporting(0);
ini_set('display_errors', '0');
mysqli_report(MYSQLI_REPORT_OFF);

// Koneksi + definisi BASE_URL (untuk URL foto) dari connection.php
include __DIR__ . '/../../script/connection.php';

header('Content-Type: application/json; charset=utf-8');

if(!isset($conn) || $conn->connect_error){
    echo json_encode([]);
    exit;
}

$conn->set_charset('utf8mb4');

$q = isset($_GET['q']) ? trim($_GET['q']) : '';

if($q === ''){
    echo json_encode([]);
    exit;
}

$safeQ = $conn->real_escape_string($q);

$query = $conn->query("
    SELECT id, code, name, category, sell_price, unit, photo, created_at
    FROM products
    WHERE deleted_at IS NULL AND (name LIKE '%$safeQ%' OR code LIKE '%$safeQ%' OR category LIKE '%$safeQ%')
    ORDER BY name ASC
    LIMIT 20
");

$results = [];

if($query && $query->num_rows > 0){
    while($d = $query->fetch_assoc()){
        // Stats
        $totalQty = 0;
        $totalTrans = 0;
        $pi = @$conn->query("SELECT COALESCE(SUM(qty),0) as tq, COUNT(id) as tt FROM purchase_items WHERE product_id = {$d['id']} AND deleted_at IS NULL");
        if($pi && $pi->num_rows > 0){
            $piRow = $pi->fetch_assoc();
            $totalQty = (int)$piRow['tq'];
            $totalTrans = (int)$piRow['tt'];
        }

        // Suppliers (multi) dari tabel relasi product_supplier
        $supplierName = '-';
        $supplierIds = [];
        $supplierNames = [];
        $sq = @$conn->query("
            SELECT s.id, s.name
            FROM product_supplier ps
            JOIN suppliers s ON s.id = ps.supplier_id
            WHERE ps.product_id = {$d['id']} AND s.deleted_at IS NULL
            ORDER BY s.name ASC
        ");
        if($sq){
            while($srow = $sq->fetch_assoc()){
                $supplierIds[] = (int)$srow['id'];
                $supplierNames[] = $srow['name'];
            }
        }
        $supplierName = $supplierNames ? implode(', ', $supplierNames) : '-';

        $results[] = [
            'id' => (int)$d['id'],
            'code' => $d['code'],
            'name' => $d['name'],
            'category' => $d['category'],
            'price' => (int)$d['sell_price'],
            'priceFormatted' => number_format((int)$d['sell_price'], 0, ',', '.'),
            'unit' => $d['unit'],
            'photo' => !empty($d['photo']) ? BASE_URL . '/assets/img/products/' . $d['photo'] : '',
            'supplier' => $supplierName,
            'supplierId' => $supplierIds ? $supplierIds[0] : '',
            'supplierNames' => $supplierNames,
            'supplierIds' => $supplierIds,
            'totalQty' => $totalQty,
            'totalQtyFormatted' => number_format($totalQty, 0, ',', '.'),
            'totalTransaksi' => $totalTrans
        ];
    }
}

$conn->close();
echo json_encode($results);