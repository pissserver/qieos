<?php
include '../../sessions/session.php';

header('Content-Type: application/json');

if(!isset($_GET['id'])){
    echo json_encode(["status"=>"error","message"=>"ID pesanan tidak diterima"]);
    exit;
}

$id = (int)$_GET['id'];

// Ambil data order
$orderQuery = $conn->prepare("SELECT * FROM orders WHERE id=?");
$orderQuery->bind_param("i", $id);
$orderQuery->execute();
$orderResult = $orderQuery->get_result();

if(!$orderResult || $orderResult->num_rows === 0){
    echo json_encode(["status"=>"error","message"=>"Pesanan tidak ditemukan"]);
    exit;
}

$order = $orderResult->fetch_assoc();
$order['tanggal'] = date('d M Y', strtotime($order['tanggal']));

// Ambil item order sekaligus data produk (photo dll)
$itemQuery = $conn->prepare("
    SELECT oi.*, p.photo, p.name as product_name
    FROM order_details oi
    LEFT JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id=?
");
$itemQuery->bind_param("i", $id);
$itemQuery->execute();
$itemResult = $itemQuery->get_result();

$items = [];
if($itemResult){
    while($row = $itemResult->fetch_assoc()){
        $items[] = $row;
    }
}

echo json_encode([
    "status" => "success",
    "order" => $order,
    "items" => $items
]);