<?php
    include '../../sessions/session.php';

    header('Content-Type: application/json');

    if (!isset($_POST['order_id'])) {
        echo json_encode(["status" => "error", "message" => "ID pesanan tidak diterima"]);
        exit;
    }

    $order_id = (int)$_POST['order_id'];

    // Pesanan harus ada dan tidak dibatalkan
    $cek = $conn->prepare("SELECT id FROM orders WHERE id=? AND status_payment != 'cancelled'");
    $cek->bind_param("i", $order_id);
    $cek->execute();
    $res = $cek->get_result();

    if (!$res || $res->num_rows === 0) {
        echo json_encode(["status" => "error", "message" => "Pesanan tidak ditemukan"]);
        exit;
    }

    // customer_id kosong / "null" / "0" = HAPUS nama customer
    $raw = isset($_POST['customer_id']) ? trim((string)$_POST['customer_id']) : '';
    $customer_id = ($raw === '' || $raw === 'null' || $raw === '0') ? null : (int)$raw;

    // Validasi customer masih ada & tidak soft-deleted
    if ($customer_id !== null) {
        $v = $conn->prepare("SELECT id FROM customers WHERE id=? AND deleted_at IS NULL");
        $v->bind_param("i", $customer_id);
        $v->execute();
        $vres = $v->get_result();

        if (!$vres || $vres->num_rows === 0) {
            echo json_encode(["status" => "error", "message" => "Customer tidak ditemukan"]);
            exit;
        }
    }

    // Semua baris detail pesanan ikut di-update (kolomnya per item)
    $stmt = $conn->prepare("UPDATE order_details SET customer_id=? WHERE order_id=?");
    $stmt->bind_param("ii", $customer_id, $order_id);

    if (!$stmt->execute()) {
        echo json_encode(["status" => "error", "message" => "Gagal menyimpan data customer"]);
        exit;
    }

    $name = null;
    if ($customer_id !== null) {
        $n = $conn->prepare("SELECT name FROM customers WHERE id=?");
        $n->bind_param("i", $customer_id);
        $n->execute();
        $nres = $n->get_result();
        $name = ($nres && $nres->num_rows > 0) ? $nres->fetch_assoc()['name'] : null;
    }

    echo json_encode([
        "status"        => "success",
        "customer_id"   => $customer_id,
        "customer_name" => $name,
    ]);
