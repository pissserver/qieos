<?php
    include '../../sessions/session.php';

    header('Content-Type: application/json');

    $action = isset($_GET['action']) ? $_GET['action'] : '';

    if($_GET['action'] === 'approve'){
        try {

            $id = (int)$_POST['id'];

            if($id <= 0){
                throw new Exception("ID tidak valid");
            }

            /* 🔥 1. AMBIL REQUEST + LOCK */
            $req = mysqli_query($conn,"
                SELECT * FROM stock_requests 
                WHERE id = $id 
                AND status = 'pending'
                LIMIT 1
                FOR UPDATE
            ");

            if(mysqli_num_rows($req) == 0){
                throw new Exception("Request tidak ditemukan / sudah diproses");
            }

            $data = mysqli_fetch_assoc($req);
            $product_id = $data['product_id'];
            $qty = $data['qty'];

            /* 🔥 2. CEK TOTAL STOK */
            $stok = mysqli_fetch_assoc(mysqli_query($conn,"
                SELECT COALESCE(SUM(remaining_qty),0) as total
                FROM purchase_items
                WHERE product_id = $product_id
            "))['total'];

            if($stok < $qty){
                throw new Exception("Stok gudang tidak cukup (sisa: $stok)");
            }

            /* 🔥 3. FIFO */
            $remaining = $qty;

            $fifo = mysqli_query($conn,"
                SELECT id, remaining_qty, date
                FROM purchase_items
                WHERE product_id = $product_id
                AND remaining_qty > 0
                ORDER BY date ASC
                FOR UPDATE
            ");

            while($row = mysqli_fetch_assoc($fifo)){

                if($remaining <= 0) break;

                $take = min($remaining, $row['remaining_qty']);

                $update = mysqli_query($conn,"
                    UPDATE purchase_items
                    SET remaining_qty = remaining_qty - $take
                    WHERE id = {$row['id']}
                ");

                if(!$update){
                    throw new Exception(mysqli_error($conn));
                }

                $remaining -= $take;
            }

            /* 🔥 4. UPDATE SALES STOCK */
            $cekSales = mysqli_query($conn,"
                SELECT id, qty 
                FROM sales_stock
                WHERE product_id = $product_id
                LIMIT 1
                FOR UPDATE
            ");

            if(mysqli_num_rows($cekSales) > 0){

                $s = mysqli_fetch_assoc($cekSales);
                $newQty = $s['qty'] + $qty;

                mysqli_query($conn,"
                    UPDATE sales_stock
                    SET qty = $newQty
                    WHERE id = {$s['id']}
                ");

            } else {

                mysqli_query($conn,"
                    INSERT INTO sales_stock (product_id, qty)
                    VALUES ($product_id, $qty)
                ");
            }

            /* 🔥 5. UPDATE STATUS REQUEST */
            mysqli_query($conn,"
                UPDATE stock_requests
                SET status = 'approved'
                WHERE id = $id
            ");

            /* 🔥 6. LOG */
            mysqli_query($conn,"
                INSERT INTO stock_transfers (product_id, qty)
                VALUES ($product_id, $qty)
            ");

            mysqli_commit($conn);

            echo json_encode([
                "status" => "success",
                "msg" => "Request berhasil di-ACC & stok dipindahkan"
            ]);
            exit;

        } catch (Exception $e){

            mysqli_rollback($conn);

            echo json_encode([
                "status" => "error",
                "msg" => $e->getMessage()
            ]);
            exit;
        }
    }

    if($action === 'reject'){

        try {

            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

            if($id <= 0){
                echo json_encode([
                    "status"=>"error",
                    "msg"=>"ID tidak valid"
                ]);
                exit;
            }

            /* 🔥 cek dulu datanya */
            $cek = mysqli_query($conn,"
                SELECT id FROM stock_requests 
                WHERE id=$id AND status='pending'
            ");

            if(mysqli_num_rows($cek) == 0){
                echo json_encode([
                    "status"=>"error",
                    "msg"=>"Data tidak ditemukan / sudah diproses"
                ]);
                exit;
            }

            /* 🔥 update */
            $update = mysqli_query($conn,"
                UPDATE stock_requests
                SET status = 'rejected'
                WHERE id=$id
            ");

            if(!$update){
                throw new Exception(mysqli_error($conn));
            }

            echo json_encode([
                "status"=>"success",
                "msg"=>"Request berhasil ditolak"
            ]);
            exit;

        } catch (Exception $e){

            echo json_encode([
                "status"=>"error",
                "msg"=>$e->getMessage()
            ]);
            exit;
        }
    }