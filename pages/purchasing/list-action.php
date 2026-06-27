<?php
    include '../../sessions/session.php';
    header('Content-Type: application/json');

    if ($_GET['action'] == 'store') {
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
    }

    if ($_GET['action'] == 'update') {

        $id = (int)$_POST['id'];
        $dateNow = date('Y-m-d');

        mysqli_query($conn,"
            DELETE FROM list_purchase_items
            WHERE list_purchase_id = '$id'
        ");

        foreach($_POST['product_name'] as $key => $name){

            $name = mysqli_real_escape_string($conn,$name);
            $qty  = (int)$_POST['qty'][$key];
            $unit = mysqli_real_escape_string($conn,$_POST['unit'][$key]);
            $price= (int)$_POST['price'][$key];

            mysqli_query($conn,"
            INSERT INTO list_purchase_items(
                list_purchase_id,
                name,
                qty,
                unit,
                price
            ) VALUES(
                '$id',
                '$name',
                '$qty',
                '$unit',
                '$price'
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

        $update = mysqli_query($conn,"
            UPDATE list_purchases
            SET deleted_at = NOW()
            WHERE id='$id'
        ");

        if($update){
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
            SELECT name, qty, unit
            FROM list_purchase_items
            WHERE list_purchase_id = '$id'
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