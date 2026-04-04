<?php
    include '../sessions/session.php';

    $data = json_decode(file_get_contents("php://input"), true);

    if(!$data){
        echo json_encode(["status"=>"error","message"=>"Data kosong"]);
        exit;
    }

    // hitung total
    $total = 0;
    foreach($data['cart'] as $item){
        $total += $item['price'] * $item['qty'];
    }

    // 1️⃣ insert ke orders
    $stmt = $conn->prepare("INSERT INTO orders (customer_name, tanggal, total) VALUES (?, NOW(), ?)");
    $stmt->bind_param("si", $data['customer_name'], $total);
    $stmt->execute();

    $order_id = $stmt->insert_id;

    // 2️⃣ insert ke order_details
    $stmt_detail = $conn->prepare("
        INSERT INTO order_details (order_id, product_id, qty, price, subtotal)
        VALUES (?, ?, ?, ?, ?)
    ");

    foreach($data['cart'] as $item){
        $subtotal = $item['price'] * $item['qty'];

        $stmt_detail->bind_param(
            "iiiii",
            $order_id,
            $item['id'],
            $item['qty'],
            $item['price'],
            $subtotal
        );

        $stmt_detail->execute();
    }

    echo json_encode([
        "status"=>"success",
        "order_id" => $order_id
    ]);

    if ($stmt->affected_rows > 0) {
        $_SESSION['success_message'] = "Pesanan berhasil dibuat";
    } else {
        $_SESSION['error_message'] = "Gagal membuat pesanan";
    }
    