<?php
    include '../../sessions/session.php';
    header('Content-Type: application/json');

    if(!isset($_POST['order_id'])){
        echo json_encode(["status"=>"error","message"=>"ID pesanan tidak diterima"]);
        exit;
    }

    $order_id = (int)$_POST['order_id'];

    $conn->begin_transaction();

    try {

        // Kunci baris order supaya aman dari klik "Bayar" ganda
        $cek = mysqli_query($conn, "
            SELECT status_payment
            FROM orders
            WHERE id = $order_id
            LIMIT 1
            FOR UPDATE
        ");

        $order = $cek ? mysqli_fetch_assoc($cek) : null;

        if(!$order){
            throw new Exception("Order tidak ditemukan");
        }

        // Order yang sudah dibatalkan tidak boleh dibayar
        if($order['status_payment'] === 'cancelled'){
            throw new Exception("Order sudah dibatalkan");
        }

        // Sudah terbayar -> idempotent: jangan potong stok dua kali
        if($order['status_payment'] === 'paid'){
            $conn->commit();
            echo json_encode(["status"=>"success"]);
            exit;
        }

        // Potong stok kantin HANYA saat pesanan benar-benar dibayar.
        // Racikan (combo) mengurangi stok tiap bahan penyusunnya.
        $detail = mysqli_query($conn, "
            SELECT product_id, product_combo_id, qty
            FROM order_details
            WHERE order_id = $order_id
        ");

        while($detail && $row = mysqli_fetch_assoc($detail)){

            // Detail racikan: product_id NULL -> potong tiap bahan penyusun
            if($row['product_id'] === null){
                $comboId  = (int)$row['product_combo_id'];
                $comboQty = (int)$row['qty'];

                if($comboId > 0 && $comboQty > 0){
                    $ingq = mysqli_query($conn, "
                        SELECT product_id, COALESCE(qty, 1) AS qty
                        FROM product_combo_items
                        WHERE combo_id = $comboId
                    ");
                    while($ingq && $ing = mysqli_fetch_assoc($ingq)){
                        $pid  = (int)$ing['product_id'];
                        $need = max(1, (int)$ing['qty']) * $comboQty;

                        $bal = mysqli_fetch_assoc(mysqli_query($conn, "
                            SELECT COALESCE(SUM(qty),0) v
                            FROM sales_stock
                            WHERE product_id = $pid
                        "));
                        $deduct = min($need, (int)$bal['v']);

                        if($deduct > 0){
                            mysqli_query($conn, "
                                INSERT INTO sales_stock (product_id, qty, type)
                                VALUES ($pid, -$deduct, 'sale')
                            ");
                        }
                    }
                }
                continue;
            }

            $pid = (int)$row['product_id'];
            $qty = (int)$row['qty'];

            $bal = mysqli_fetch_assoc(mysqli_query($conn, "
                SELECT COALESCE(SUM(qty),0) v
                FROM sales_stock
                WHERE product_id = $pid
            "));
            $deduct = min($qty, (int)$bal['v']);

            if($deduct > 0){
                mysqli_query($conn, "
                    INSERT INTO sales_stock (product_id, qty, type)
                    VALUES ($pid, -$deduct, 'sale')
                ");
            }
        }

        // Update status_payment
        $stmt = $conn->prepare("UPDATE orders SET status_payment='paid' WHERE id=?");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();

        $conn->commit();

        echo json_encode(["status"=>"success"]);

    } catch(Exception $e){
        $conn->rollback();
        echo json_encode(["status"=>"error","message"=>$e->getMessage()]);
    }
