<?php
    include '../../sessions/session.php';

    header('Content-Type: application/json');

    $action = isset($_GET['action']) ? $_GET['action'] : '';

    if($action === 'approve'){
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

            /* 🔥 2. AMBIL SEMUA ITEM */
            $items = mysqli_query($conn,"
                SELECT product_id, qty
                FROM stock_request_items
                WHERE request_id = $id
                ORDER BY id ASC
                FOR UPDATE
            ");

            if(mysqli_num_rows($items) == 0){
                throw new Exception("Request tidak memiliki item");
            }

            /* 🔥 3. PROSES SETIAP ITEM (FIFO) */
            while($it = mysqli_fetch_assoc($items)){

                $product_id = (int)$it['product_id'];
                $qty = (int)$it['qty'];

                /* CEK TOTAL STOK GUDANG */
                $stok = mysqli_fetch_assoc(mysqli_query($conn,"
                    SELECT COALESCE(SUM(remaining_qty),0) as total
                    FROM purchase_items
                    WHERE product_id = $product_id
                    AND deleted_at IS NULL
                "))['total'];

                if($stok < $qty){
                    $p = mysqli_fetch_assoc(mysqli_query($conn,"
                        SELECT name FROM products WHERE id = $product_id
                    "));

                    throw new Exception("Stok " . (isset($p['name']) && $p['name'] ? $p['name'] : "produk") . " tidak cukup (sisa: $stok)");
                }

                /* FIFO */
                $remaining = $qty;

                $fifo = mysqli_query($conn,"
                    SELECT pi.id, pi.remaining_qty, p.date
                    FROM purchase_items pi
                    JOIN purchases p ON p.id = pi.purchase_id
                    WHERE pi.product_id = $product_id
                    AND pi.remaining_qty > 0
                    AND pi.deleted_at IS NULL
                    ORDER BY p.date ASC
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

                /* CATAT MOVEMENT KE SALES STOCK (ledger) */
                $log = mysqli_query($conn,"
                    INSERT INTO sales_stock (product_id, qty, type)
                    VALUES ($product_id, $qty, 'transfer')
                ");

                if(!$log){
                    throw new Exception(mysqli_error($conn));
                }
            }

            /* 🔥 4. UPDATE STATUS REQUEST */
            mysqli_query($conn,"
                UPDATE stock_requests
                SET status = 'approved',
                    approved_by = ".(int)$_SESSION['user_id'].",
                    approved_at = NOW()
                WHERE id = $id
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
                SET status = 'rejected',
                    approved_by = ".(int)$_SESSION['user_id'].",
                    approved_at = NOW()
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
                "status" => "error",
                "msg" => $e->getMessage()
            ]);
            exit;
        }
    }