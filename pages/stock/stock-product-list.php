<?php
include '../../sessions/session.php';

$q = mysqli_query($conn, "
    SELECT code, name, category
    FROM products
    ORDER BY name ASC
");

$data = [];

while($d = mysqli_fetch_assoc($q)){
    $data[] = $d;
}

header('Content-Type: application/json');
echo json_encode($data);