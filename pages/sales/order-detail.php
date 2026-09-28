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

// Ambil customer pesanan (bisa NULL = pesanan tanpa nama)
$customerQuery = $conn->prepare("
    SELECT c.id, c.name, c.phone
    FROM order_details od
    JOIN customers c ON c.id = od.customer_id
    WHERE od.order_id=? AND od.customer_id IS NOT NULL
    LIMIT 1
");
$customerQuery->bind_param("i", $id);
$customerQuery->execute();
$customerResult = $customerQuery->get_result();

$customer = null;
if($customerResult && $customerResult->num_rows > 0){
    $customer = $customerResult->fetch_assoc();
    $customer['id'] = (int)$customer['id'];
}

// Ambil item order sekaligus data produk (photo dll) & data racikan
$itemQuery = $conn->prepare("
    SELECT oi.*, p.photo, COALESCE(oi.name, p.name, pc.name, 'Barang') AS product_name
    FROM order_details oi
    LEFT JOIN products p ON oi.product_id = p.id
    LEFT JOIN product_combos pc ON oi.product_combo_id = pc.id
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
    "customer" => $customer,
    "items" => $items
]);