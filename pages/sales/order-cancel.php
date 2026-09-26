<?php
    include '../../sessions/session.php';

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
            SELECT product_id, qty
            FROM order_details
            WHERE order_id = $order_id
        ");

        while ($row = mysqli_fetch_assoc($detail)) {

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