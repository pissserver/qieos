<?php
require_once __DIR__ . '/../../../sessions/session.php';
require_once __DIR__ . '/stock-status.php';

header('Content-Type: application/json');

$role = $user['role'];
$items = [];

if ($role === 'staff kasir') {
    $query = "
        SELECT t.id, t.code, t.name, t.low_stock, t.low_stock_kantin, t.stock
        FROM (
            SELECT 
                p.id,
                p.code,
                p.name,
                p.low_stock,
                p.low_stock_kantin,
                COALESCE(SUM(ss.qty), 0) as stock
            FROM products p
            INNER JOIN sales_stock ss ON ss.product_id = p.id
            WHERE p.category != 'Additional'
            GROUP BY p.id
        ) t
        WHERE t.stock <= IFNULL(t.low_stock_kantin, 0)
        ORDER BY t.stock ASC, t.name ASC
    ";
} else {
    $query = "
        SELECT t.id, t.code, t.name, t.low_stock, t.stock
        FROM (
            SELECT 
                p.id,
                p.code,
                p.name,
                p.low_stock,
                COALESCE(SUM(pi.remaining_qty), 0) as stock
            FROM products p
            LEFT JOIN purchase_items pi ON pi.product_id = p.id AND pi.deleted_at IS NULL
            LEFT JOIN purchases pu ON pu.id = pi.purchase_id AND pu.deleted_at IS NULL
            WHERE p.category != 'Additional'
            GROUP BY p.id
        ) t
        WHERE t.stock <= IFNULL(t.low_stock, 0)
        ORDER BY t.stock ASC, t.name ASC
    ";
}

$result = mysqli_query($conn, $query);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $stock = (int)$row['stock'];
        // Staff kasir (kantin) memakai batas kantin per-produk;
        // role lain (gudang) memakai low_stock per-produk.
        $lowStock = ($role === 'staff kasir')
            ? resolve_product_low_stock_kantin($conn, $row)
            : resolve_product_low_stock($conn, $row);
        
        $status = 'menipis';
        if ($stock <= 0) {
            $status = 'habis';
        }
        
        $items[] = [
            'id' => (int)$row['id'],
            'code' => $row['code'],
            'name' => $row['name'],
            'stock' => $stock,
            'low_stock' => $lowStock,
            'status' => $status
        ];
    }
}

echo json_encode([
    'status' => 'success',
    'count' => count($items),
    'items' => $items,
    'role' => $role
]);
