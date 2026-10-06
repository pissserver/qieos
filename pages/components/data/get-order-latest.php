<?php
    include '../../../sessions/session.php';

    // Dipakai order.php untuk mendeteksi pesanan baru secara real-time
    // tanpa reload halaman. Query ringan: 1 request, 1 scan 50 baris terakhir.
    $query = mysqli_query($conn, "
        SELECT id, code, tanggal, status_payment
        FROM (
            SELECT id, code, tanggal, status_payment
            FROM orders
            WHERE status_payment != 'cancelled'
            ORDER BY id DESC
            LIMIT 50
        ) AS sub
        ORDER BY id DESC
    ");

    $latest  = ['id' => 0, 'code' => '', 'tanggal' => '', 'status_payment' => ''];
    $waiting = 0;
    $paid    = 0;
    $rows    = 0;

    while ($row = mysqli_fetch_assoc($query)) {
        if ($rows === 0) $latest = $row;
        $rows++;
        if ($row['status_payment'] === 'paid') $paid++; else $waiting++;
    }

    // id pesanan terakhir yang dibatalkan: dipakai realtime sync supaya
    // pembatalan tetap terdeteksi walau jumlah waiting/paid tidak berubah.
    $cancelledId = 0;
    $cancelQ = mysqli_query($conn, "SELECT MAX(id) AS max_id FROM orders WHERE status_payment='cancelled'");
    if ($cancelQ && ($cancelR = mysqli_fetch_assoc($cancelQ))) $cancelledId = (int)$cancelR['max_id'];

    header('Content-Type: application/json');

    echo json_encode([
        "latest_id"    => (int)$latest['id'],
        "code"         => $latest['code'],
        "tanggal"      => $latest['tanggal'],
        "status"       => $latest['status_payment'],
        "total"        => $rows,
        "waiting"      => $waiting,
        "paid"         => $paid,
        "cancelled_id" => $cancelledId,
    ]);
