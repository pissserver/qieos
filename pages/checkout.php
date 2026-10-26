<?php
    include '../sessions/session.php';

    $data = json_decode(file_get_contents("php://input"), true);

    if(!$data){
        echo json_encode(["status"=>"error","message"=>"Data kosong"]);
        exit;
    }

    header('Content-Type: application/json');

    // Customer opsional. Null / kosong = pesanan tanpa nama customer.
    $customerId = isset($data['customer_id']) && $data['customer_id'] !== '' && $data['customer_id'] !== null
        ? (int)$data['customer_id']
        : null;

    if($customerId !== null){
        $cek = mysqli_query($conn, "SELECT id FROM customers WHERE id = $customerId AND deleted_at IS NULL");
        if(mysqli_num_rows($cek) === 0) $customerId = null;
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
    $stmt = $conn->prepare("INSERT INTO orders (staff_id, code, tanggal, total) VALUES (?, ?, NOW(), ?)");
    $stmt->bind_param("iss", $_SESSION['user_id'], $code, $total);
    $stmt->execute();

    $order_id = $stmt->insert_id;

    // 2️⃣ insert ke order_details
    // Stok kantin TIDAK dipotong di sini. Pemotongan dilakukan saat
    // pesanan benar-benar dibayar (order-pay.php), supaya pesanan yang
    // masih 'waiting' belum mengurangi stok.
    //
    // Racikan (combo) dipecah jadi item per bahan penyusun: tiap bahan
    // disimpan sebagai baris tersendiri dengan product_id bahannya,
    // product_combo_id racikannya, dan qty = qty bahan × qty racikan.
    $stmt_detail = $conn->prepare("
        INSERT INTO order_details (order_id, product_id, product_combo_id, customer_id, qty, price, subtotal)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    foreach($data['cart'] as $item){
        $isCombo = !empty($item['type']) && $item['type'] === 'combo';

        if($isCombo){
            $comboId  = (int)ltrim((string)$item['id'], 'c');
            $comboQty = (int)$item['qty'];

            $ings = mysqli_query($conn, "
                SELECT product_id, qty, price
                FROM product_combo_items
                WHERE combo_id = $comboId
            ");

            while($ings && $ing = mysqli_fetch_assoc($ings)){
                $prodId   = (int)$ing['product_id'];
                $qty      = max(1, (int)$ing['qty']) * $comboQty;
                $price    = (int)$ing['price'];
                $subtotal = $price * $qty;

                $stmt_detail->bind_param(
                    "iiiiiii",
                    $order_id,
                    $prodId,
                    $comboId,
                    $customerId,
                    $qty,
                    $price,
                    $subtotal
                );

                $stmt_detail->execute();
            }

            continue;
        }

        // Produk biasa: product_id dipakai, product_combo_id NULL
        $prodId   = (int)$item['id'];
        $qty      = (int)$item['qty'];
        $price    = (int)$item['price'];
        $subtotal = $price * $qty;

        $comboId = null;

        $stmt_detail->bind_param(
            "iiiiiii",
            $order_id,
            $prodId,
            $comboId,
            $customerId,
            $qty,
            $price,
            $subtotal
        );

        $stmt_detail->execute();
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
    