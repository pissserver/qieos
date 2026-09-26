<?php
    include '../../sessions/session.php';
    header('Content-Type: application/json');

    $data = json_decode(file_get_contents("php://input"), true);

    if(!is_array($data)){
        echo json_encode(["status"=>"error","msg"=>"Data tidak valid"]);
        exit;
    }

    $action     = isset($data['action']) ? $data['action'] : 'create';
    $request_id = isset($data['request_id']) ? (int)$data['request_id'] : 0;

    /* 🔥 DELETE */
    if($action === 'delete'){

        if($request_id <= 0){
            echo json_encode(["status"=>"error","msg"=>"ID tidak valid"]);
            exit;
        }

        $conn->begin_transaction();

        try {
            $cek = mysqli_query($conn,"
                SELECT id FROM stock_requests
                WHERE id=$request_id AND status='pending'
                LIMIT 1
                FOR UPDATE
            ");

            if(mysqli_num_rows($cek) == 0){
                throw new Exception("Request tidak ditemukan atau sudah diproses");
            }

            mysqli_query($conn,"
                DELETE FROM stock_request_items
                WHERE request_id = $request_id
            ");

            mysqli_query($conn,"
                DELETE FROM stock_requests
                WHERE id = $request_id
            ");

            $conn->commit();

            echo json_encode([
                "status"=>"success",
                "msg"=>"Request berhasil dihapus"
            ]);
            exit;

        } catch(Exception $e){
            $conn->rollback();
            echo json_encode([
                "status"=>"error",
                "msg"=>$e->getMessage()
            ]);
            exit;
        }
    }

    /* 🔥 VALIDASI ITEMS (create & update) */
    if(empty($data['items'])){
        echo json_encode([
            "status"=>"error",
            "msg"=>"Minimal 1 produk harus diisi"
        ]);
        exit;
    }

    $items = [];
    $prev  = [];

    foreach($data['items'] as $it){

        $pid = isset($it['product_id']) ? (int)$it['product_id'] : 0;
        $qty = isset($it['qty']) ? (int)$it['qty'] : 0;

        if($pid <= 0 || $qty <= 0){
            echo json_encode([
                "status"=>"error",
                "msg"=>"Produk & qty tidak valid"
            ]);
            exit;
        }

        if(isset($prev[$pid])){
            echo json_encode([
                "status"=>"error",
                "msg"=>"Produk tidak boleh duplikat dalam 1 request"
            ]);
            exit;
        }

        $prev[$pid] = true;
        $items[] = ['product_id'=>$pid, 'qty'=>$qty];
    }

    /* 🔥 VALIDASI STOK GUDANG SETIAP ITEM */
    foreach($items as $it){
        $pid = $it['product_id'];

        $stok = mysqli_fetch_assoc(mysqli_query($conn,"
            SELECT COALESCE(SUM(remaining_qty),0) total
            FROM purchase_items
            WHERE product_id = $pid
            AND deleted_at IS NULL
        "))['total'];

        if((int)$stok < $it['qty']){

            $p = mysqli_fetch_assoc(mysqli_query($conn,"
                SELECT name FROM products WHERE id = $pid
            "));

            $name = (isset($p['name']) && $p['name']) ? $p['name'] : "Produk";

            echo json_encode([
                "status"=>"error",
                "msg"=>$name.": stok tidak cukup (sisa: $stok)"
            ]);
            exit;
        }
    }

    /* 🔥 UPDATE */
    if($action === 'update'){

        if($request_id <= 0){
            echo json_encode([
                "status"=>"error",
                "msg"=>"Request ID tidak valid"
            ]);
            exit;
        }

        $conn->begin_transaction();

        try {

            $cek = mysqli_query($conn,"
                SELECT id FROM stock_requests
                WHERE id = $request_id
                AND status = 'pending'
                LIMIT 1
                FOR UPDATE
            ");

            if(mysqli_num_rows($cek) == 0){
                throw new Exception("Request tidak ditemukan atau sudah diproses");
            }

            /* hapus item lama */
            mysqli_query($conn,"
                DELETE FROM stock_request_items
                WHERE request_id = $request_id
            ");

            /* insert item baru */
            foreach($items as $it){
                $ins = mysqli_query($conn,"
                    INSERT INTO stock_request_items
                    (request_id, product_id, qty)
                    VALUES ($request_id, {$it['product_id']}, {$it['qty']})
                ");

                if(!$ins){
                    throw new Exception(mysqli_error($conn));
                }
            }

            $conn->commit();

            echo json_encode([
                "status"=>"success",
                "msg"=>"Request berhasil diperbarui"
            ]);
            exit;

        } catch(Exception $e){
            $conn->rollback();
            echo json_encode([
                "status"=>"error",
                "msg"=>$e->getMessage()
            ]);
            exit;
        }
    }

    /* 🔥 CREATE (default) */
    $conn->begin_transaction();

    try {

        $insert = mysqli_query($conn,"
            INSERT INTO stock_requests (code, status, created_by, created_at)
            VALUES ('', 'pending', ".(int)$_SESSION['user_id'].", NOW())
        ");

        if(!$insert) throw new Exception(mysqli_error($conn));

        $request_id = $conn->insert_id;

        $code = 'REQ-' . str_pad($request_id, 7, '0', STR_PAD_LEFT);

        mysqli_query($conn,"
            UPDATE stock_requests SET code = '$code' WHERE id = $request_id
        ");

        foreach($items as $it){
            $ins = mysqli_query($conn,"
                INSERT INTO stock_request_items
                (request_id, product_id, qty)
                VALUES ($request_id, {$it['product_id']}, {$it['qty']})
            ");

            if(!$ins) throw new Exception(mysqli_error($conn));
        }

        $conn->commit();

        echo json_encode([
            "status"=>"success",
            "msg"=>"Request $code berhasil dikirim ke gudang"
        ]);
        exit;

    } catch(Exception $e){
        $conn->rollback();
        echo json_encode([
            "status"=>"error",
            "msg"=>$e->getMessage()
        ]);
        exit;
    }