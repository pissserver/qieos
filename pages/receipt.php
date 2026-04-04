<?php
include '../sessions/session.php';

$order_id = $_GET['id'];

$user = mysqli_query($conn, "
    SELECT * FROM users WHERE email = '".$_SESSION['email']."'
")->fetch_assoc();

$order = mysqli_query($conn, "
    SELECT * FROM orders WHERE id = $order_id
")->fetch_assoc();

$details = mysqli_query($conn, "
    SELECT od.*, p.product_name 
    FROM order_details od
    JOIN products p ON od.product_id = p.id
    WHERE od.order_id = $order_id
    ORDER BY p.price DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Struk Pembelian - Cartify</title>
    <link
        rel="icon"
        sizes="120x120"
        href="../assets/img/brand/cartify2.png"
    />

    <style>
        body {
            font-family: monospace;
            font-size: 15px;
            margin: 0;

            display: flex;
            justify-content: center;  /* tengah horizontal */
            align-items: center;      /* tengah vertical */
            height: 80vh;            /* full tinggi layar */
            background: #f5f5f5;      /* optional biar kelihatan */
        }

        .receipt {
            width: 300px;
            background: #fff;
            padding: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .center {
            text-align: center;
        }

        .center p {
            margin:5px 0;
        }

        hr {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }

        .item {
            margin-bottom: 6px;
        }

        .row {
            display: flex;
            justify-content: space-between;
        }

        .total {
            font-size: 14px;
        }

        @media print {
            body {
                display: block;
                background: none;
                height: auto;
            }

            .receipt {
                /* width: 58mm; */
                margin: 0;
                padding: 5px;
                box-shadow: none;
            }
        }
        
        /* @page {
            size: 58mm auto;
            margin: 0;
        } */
    </style>

</head>

<body>
    <div class="receipt">
        <div class="center">
            <h3>CARTIFY</h3>
            <p><?= $user['city'] ?> - <?= date('d M Y', strtotime($order['tanggal'])) ?></p>
            <p>Pelanggan: <b><?= $order['customer_name'] ?></b></p>
        </div>

        <hr>

        <?php while($d = mysqli_fetch_assoc($details)) { ?>
        <div class="item">
            <div><?= $d['product_name'] ?></div>
            <div class="row">
                <span><?= $d['qty'] ?> x <?= number_format($d['price']) ?></span>
                <span>Rp <?= number_format($d['subtotal']) ?></span>
            </div>
        </div>
        <?php } ?>

        <hr>

        <div class="row total">
            <span><b>Total</b></span>
            <span><b>Rp <?= number_format($order['total']) ?></b></span>
        </div>

        <hr>
        <p class="center">--- Terima kasih ---</p>
    </div>

    <script>
    window.onload = function() {
        setTimeout(() => {
            window.print();
        }, 300); // delay biar render dulu
    };
    </script>
</body>
</html>