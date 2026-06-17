<?php
    include '../../sessions/session.php';
    header('Content-Type: application/json');

    $products   = $_POST['product_name'];
    $qtys        = $_POST['qty'];
    $units       = $_POST['unit'];
    $dateNow    = date('Y-m-d');

    // Insert ke tabel list_purchases
    mysqli_query($conn, "INSERT INTO list_purchases (date_list) VALUES ('$dateNow')");
    $list_purchase_id = mysqli_insert_id($conn);

    for ($i=0; $i < count($products); $i++) { 
        $product = mysqli_real_escape_string($conn,$products[$i]);
        $qty     = mysqli_real_escape_string($conn,$qtys[$i]);
        $unit    = mysqli_real_escape_string($conn,$units[$i]);

        // Insert ke table list_purchase_items
        mysqli_query($conn, "INSERT INTO list_purchase_items (list_purchase_id, name, qty, unit) VALUES ('$list_purchase_id', '$product', '$qty', '$unit')");
    }

    echo json_encode([
        "status" => "success",
        "msg" => "Daftar belanja berhasil dibuat"
    ]);