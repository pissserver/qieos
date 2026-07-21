<?php
include '../../sessions/session.php';

$q = mysqli_query($conn, "
    SELECT DISTINCT p.code, p.name, p.category, p.sell_price, pi.unit
    FROM products p
    LEFT JOIN purchase_items pi ON p.id = pi.product_id
    ORDER BY name ASC
");

$data = [];

while($d = mysqli_fetch_assoc($q)){
    $data[] = $d;
}

header('Content-Type: application/json');
echo json_encode($data);