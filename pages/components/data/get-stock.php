<?php
    include '../../../script/connection.php';

    $res = mysqli_query($conn, "SELECT product_id, qty FROM sales_stock");

    $data = [];

    while ($row = mysqli_fetch_assoc($res)) {
        $data[$row['product_id']] = $row['qty'];
    }

    echo json_encode($data);