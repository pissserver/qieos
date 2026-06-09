<?php
    include '../../sessions/session.php';

    $data = json_decode(file_get_contents("php://input"), true);

    if(!isset($_POST['order_id'])){
        echo json_encode(["status"=>"error","message"=>"ID pesanan tidak diterima"]);
        exit;
    }

    $order_id = (int)$_POST['order_id'];

    // Update status_payment
    $query = "UPDATE orders SET status_payment='cancelled' WHERE id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $order_id);
    if($stmt->execute()){
        echo json_encode(["status"=>"success"]);
    } else {
        echo json_encode(["status"=>"error","message"=>"Gagal update database"]);
    }
