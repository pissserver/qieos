<?php
    include '../../sessions/session.php';
    header('Content-Type: application/json');

    if ($_GET['action'] == 'store') {
        $product_ids = isset($_POST['product_id']) ? $_POST['product_id'] : [];
        $qtys_buy    = isset($_POST['qty_buy']) ? $_POST['qty_buy'] : [];
        $units_buy   = isset($_POST['unit_buy']) ? $_POST['unit_buy'] : [];
        $prices_buy  = isset($_POST['price_buy']) ? $_POST['price_buy'] : [];

        $qLast = mysqli_query($conn,"SELECT MAX(CAST(REPLACE(form, 'FORM-', '') AS UNSIGNED)) AS last_num FROM purchases");
        $dLast = mysqli_fetch_assoc($qLast);
        $nextNum = ($dLast && $dLast['last_num'] ? (int)$dLast['last_num'] : 0) + 1;
        $formNumber = 'FORM-' . str_pad($nextNum, 7, '0', STR_PAD_LEFT);
        $dateNow = date('Y-m-d');

        mysqli_query($conn, "INSERT INTO purchases (form, date) VALUES ('$formNumber', '$dateNow')");
        $purchase_id = mysqli_insert_id($conn);

        for ($i = 0; $i < count($product_ids); $i++) {
            $product_id = (int)$product_ids[$i];
            if ($product_id <= 0) continue;

            $qty_buy   = mysqli_real_escape_string($conn, isset($qtys_buy[$i]) ? $qtys_buy[$i] : 0);
            $unit_buy  = mysqli_real_escape_string($conn, isset($units_buy[$i]) ? $units_buy[$i] : '');
            $price_buy = isset($prices_buy[$i]) && $prices_buy[$i] !== ''
                ? (float)$prices_buy[$i]
                : 0;

            mysqli_query($conn, "INSERT INTO purchase_items
                (purchase_id, product_id, qty_buy, unit_buy, price_buy)
                VALUES
                ($purchase_id, $product_id, $qty_buy, '$unit_buy', $price_buy)");
        }

        echo json_encode([
            "status" => "success",
            "msg" => "Daftar belanja berhasil dibuat"
        ]);
    }

    if ($_GET['action'] == 'update') {

        $id = (int)$_POST['id'];

        mysqli_query($conn,"
            DELETE FROM purchase_items
            WHERE purchase_id = '$id'
        ");

        $product_ids = isset($_POST['product_id']) ? $_POST['product_id'] : [];

        foreach($product_ids as $key => $product_id){
            $product_id = (int)$product_id;
            if ($product_id <= 0) continue;

            $qty_buy   = isset($_POST['qty_buy'][$key]) ? mysqli_real_escape_string($conn, $_POST['qty_buy'][$key]) : 0;
            $unit_buy  = mysqli_real_escape_string($conn, isset($_POST['unit_buy'][$key]) ? $_POST['unit_buy'][$key] : '');
            $price_buy = isset($_POST['price_buy'][$key]) && $_POST['price_buy'][$key] !== ''
                ? (float)$_POST['price_buy'][$key]
                : 0;

            mysqli_query($conn,"
            INSERT INTO purchase_items(
                purchase_id,
                product_id,
                qty_buy,
                unit_buy,
                price_buy
            ) VALUES(
                '$id',
                '$product_id',
                '$qty_buy',
                '$unit_buy',
                '$price_buy'
            )
            ");
        }

        echo json_encode([
            "status" => "success",
            "msg" => "Daftar belanja berhasil diupdate"
        ]);
    }

    if ($_GET['action'] == 'destroy') {
        $id = (int)$_POST['id'];

        $u1 = mysqli_query($conn,"
            UPDATE purchases
            SET deleted_at = NOW()
            WHERE id='$id'
        ");

        $u2 = mysqli_query($conn,"
            UPDATE purchase_items
            SET deleted_at = NOW()
            WHERE purchase_id='$id'
        ");

        if($u1 && $u2){
            echo json_encode([
                'status'=>'success',
                "msg" => "Daftar belanja berhasil dihapus"
            ]);
        }else{
            echo json_encode([
                'status'=>'error',
                'msg'=>mysqli_error($conn)
            ]);
        }

        exit;
    }

    if ($_GET['action'] == 'get_print') {

        $id = (int)$_GET['id'];

        $q = mysqli_query($conn,"
            SELECT
                COALESCE(products.name, '') AS name,
                pi.qty_buy AS qty,
                pi.unit_buy AS unit,
                pi.price_buy AS price
            FROM purchase_items pi
            LEFT JOIN products
                ON products.id = pi.product_id
            WHERE pi.purchase_id = '$id'
              AND pi.deleted_at IS NULL
        ");

        $data = [];

        while($d = mysqli_fetch_assoc($q)){
            $data[] = $d;
        }

        echo json_encode([
            "status" => "success",
            "data" => $data
        ]);

        exit;
    }