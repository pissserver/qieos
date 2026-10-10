<?php
    include '../../sessions/session.php';

    if ($user['role'] !== 'developer') {
        echo json_encode([
            "status" => "error",
            "message" => "Akses ditolak"
        ]);
        exit;
    }

    if (!isset($_POST['order_id'])) {
        echo json_encode([
            "status" => "error",
            "message" => "ID pesanan tidak diterima"
        ]);
        exit;
    }

    $order_id = (int) $_POST['order_id'];

    // Hindari cancel dua kali
    $cek = mysqli_query($conn, "
        SELECT status_payment
        FROM orders
        WHERE id = $order_id
    ");

    $order = mysqli_fetch_assoc($cek);

    if (!$order) {
        echo json_encode([
            "status" => "error",
            "message" => "Order tidak ditemukan"
        ]);
        exit;
    }

    if ($order['status_payment'] == 'cancelled') {
        echo json_encode([
            "status" => "error",
            "message" => "Order sudah dibatalkan"
        ]);
        exit;
    }

    mysqli_begin_transaction($conn);

    try {

        // Ambil semua detail order
        $detail = mysqli_query($conn, "
            SELECT id, product_id, product_combo_id, qty
            FROM order_details
            WHERE order_id = $order_id
        ");

        while ($row = mysqli_fetch_assoc($detail)) {

            // Detail racikan: product_id NULL -> kembalikan stok kantin
            // seluruh bahan penyusunnya.
            if ($row['product_id'] === null) {
                $comboId  = (int)$row['product_combo_id'];
                $comboQty = (int)$row['qty'];

                if($comboId > 0 && $comboQty > 0){
                    $ingq = mysqli_query($conn, "
                        SELECT product_id, COALESCE(qty, 1) AS qty
                        FROM product_combo_items
                        WHERE combo_id = $comboId
                    ");
                    while($ingq && $ing = mysqli_fetch_assoc($ingq)){
                        $restore = max(1, (int)$ing['qty']) * $comboQty;
                        mysqli_query($conn, "
                            INSERT INTO sales_stock (product_id, qty, type)
                            VALUES (" . (int)$ing['product_id'] . ", $restore, 'return')
                        ");
                    }
                }
                continue;
            }

            mysqli_query($conn, "
                INSERT INTO sales_stock (product_id, qty, type)
                VALUES ({$row['product_id']}, {$row['qty']}, 'return')
            ");
        }

        // Update status order
        $stmt = $conn->prepare("
            UPDATE orders
            SET status_payment='cancelled'
            WHERE id=?
        ");

        $stmt->bind_param("i", $order_id);
        $stmt->execute();

        mysqli_commit($conn);

        echo json_encode([
            "status" => "success"
        ]);

    } catch (Exception $e) {

        mysqli_rollback($conn);

        echo json_encode([
            "status" => "error",
            "message" => $e->getMessage()
        ]);
    }