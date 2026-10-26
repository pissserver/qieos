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

// Ambil item order sekaligus data produk & data racikan.
// Racikan (product_combo_id) digabung jadi SATU baris memakai nama combo;
// rincian tiap bahan penyusunnya dilihat lewat ikon mata (combo-detail.php).
$itemQuery = $conn->prepare("
    SELECT oi.*, p.photo, p.name AS product_name, pc.name AS combo_name,
           COALESCE(pci.qty, 1) AS recipe_qty
    FROM order_details oi
    LEFT JOIN products p ON oi.product_id = p.id
    LEFT JOIN product_combos pc ON oi.product_combo_id = pc.id
    LEFT JOIN product_combo_items pci
           ON pci.combo_id = oi.product_combo_id AND pci.product_id = oi.product_id
    WHERE oi.order_id=?
    ORDER BY oi.id ASC
");
$itemQuery->bind_param("i", $id);
$itemQuery->execute();
$itemResult = $itemQuery->get_result();

$items = [];
$comboIndex = [];
if($itemResult){
    while($row = $itemResult->fetch_assoc()){

        // Baris racikan -> gabungkan per kombo
        if($row['product_combo_id'] !== null){
            $cid = (int)$row['product_combo_id'];

            if(!isset($comboIndex[$cid])){
                $recipeQty = max(1, (int)$row['recipe_qty']);
                $comboQty  = $recipeQty > 0 ? (int)floor((int)$row['qty'] / $recipeQty) : (int)$row['qty'];
                if($comboQty < 1) $comboQty = 1;

                $comboIndex[$cid] = count($items);
                $items[] = [
                    'id'               => (int)$row['id'],
                    'order_id'         => (int)$row['order_id'],
                    'product_id'       => null,
                    'product_combo_id' => $cid,
                    'customer_id'      => $row['customer_id'],
                    'qty'              => $comboQty,
                    'price'            => (int)round((int)$row['subtotal'] / $comboQty),
                    'subtotal'         => (int)$row['subtotal'],
                    'photo'            => '',
                    'product_name'     => $row['combo_name'] !== null ? $row['combo_name'] : 'Racikan',
                ];
                continue;
            }

            $idx = $comboIndex[$cid];
            $items[$idx]['subtotal'] += (int)$row['subtotal'];
            $items[$idx]['price'] = (int)round($items[$idx]['subtotal'] / max(1, (int)$items[$idx]['qty']));
            continue;
        }

        // Produk biasa
        $items[] = [
            'id'               => (int)$row['id'],
            'order_id'         => (int)$row['order_id'],
            'product_id'       => $row['product_id'] !== null ? (int)$row['product_id'] : null,
            'product_combo_id' => null,
            'customer_id'      => $row['customer_id'],
            'qty'              => (int)$row['qty'],
            'price'            => (int)$row['price'],
            'subtotal'         => (int)$row['subtotal'],
            'photo'            => $row['photo'],
            'product_name'     => $row['product_name'] !== null ? $row['product_name'] : 'Barang',
        ];
    }
}

echo json_encode([
    "status" => "success",
    "order" => $order,
    "customer" => $customer,
    "items" => $items
]);