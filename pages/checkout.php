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

    // Generate code unik untuk pesanan
    $dateCode = date('Ymd');
    $q = mysqli_query($conn,"
        SELECT code
        FROM orders
        WHERE DATE(tanggal)=CURDATE()
        ORDER BY id DESC
        LIMIT 1
    ");

    if(mysqli_num_rows($q) > 0){

        $last = mysqli_fetch_assoc($q);

        $lastNumber = (int)substr($last['code'], -5);

        $nextNumber = $lastNumber + 1;

    }else{

        $nextNumber = 1;

    }

    $code = $dateCode . '-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

    // 1️⃣ insert ke orders
    $stmt = $conn->prepare("INSERT INTO orders (code, tanggal, total) VALUES (?, NOW(), ?)");
    $stmt->bind_param("si", $code, $total);
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

        mysqli_query($conn, "
            UPDATE sales_stock
            SET qty = GREATEST(qty - {$item['qty']}, 0)
            WHERE product_id = {$item['id']}
        ");
    }

    echo json_encode([
        "status" => "success",
        "order_id" => $order_id,
    ]);

    if ($stmt->affected_rows > 0) {
        $_SESSION['success_message'] = "Pesanan berhasil dibuat";
    } else {
        $_SESSION['error_message'] = "Gagal membuat pesanan";
    }
    