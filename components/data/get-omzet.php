<?php
    include '../../script/connection.php';

    $today = date('Y-m-d');

    $query = $conn->prepare("
        SELECT SUM(total) as omzet 
        FROM orders 
        WHERE DATE(tanggal)=? 
        AND status_payment!='cancelled'
    ");
    $query->bind_param("s", $today);
    $query->execute();

    $result = $query->get_result()->fetch_assoc();

    $omzet = $result['omzet'] !== null ? $result['omzet'] : 0;

    echo json_encode([
        "omzet" => number_format($omzet, 0, ',', '.')
    ]);