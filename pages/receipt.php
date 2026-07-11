<?php
include '../sessions/session.php';

$order_id = $_GET['id'];

$user = mysqli_query($conn, "
    SELECT * FROM users WHERE username = '" . $_SESSION['username'] . "'
")->fetch_assoc();

$order = mysqli_query($conn, "
    SELECT * FROM orders WHERE id = $order_id
")->fetch_assoc();

$details = mysqli_query($conn, "
    SELECT od.*, p.name AS product_name 
    FROM order_details od
    JOIN products p ON od.product_id = p.id
    WHERE od.order_id = $order_id
    ORDER BY p.name ASC
");
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Struk Pembelian - Qieos</title>
    <link
        rel="icon"
        sizes="120x120"
        href="../assets/img/brand/qieos2.png" />

    <style>
        body{
            margin:0;
            padding:20px;
            background:#ececec;
            font-family: monospace;
            font-size:12px;

            display:flex;
            justify-content:center;
        }

        .receipt{
            width:300px;
            background:#fff;
            padding:14px;
            box-sizing:border-box;
            box-shadow:0 5px 20px rgba(0,0,0,.15);
        }

        .center{
            text-align:center;
        }

        .logo{
            font-size:22px;
            font-weight:bold;
            letter-spacing:3px;
        }

        .store{
            font-size:11px;
            color:#555;
        }

        .receipt-title{
            font-size:13px;
            font-weight:bold;
            margin-top:3px;
        }

        hr{
            border:none;
            border-top:1px dashed #000;
            margin:8px 0;
        }

        .row{
            display:flex;
            justify-content:space-between;
        }

        .info{
            margin:3px 0;
        }

        .item{
            margin:8px 0;
        }

        .item-name{
            font-weight:bold;
            text-transform:capitalize;
        }

        .item-detail{
            display:flex;
            justify-content:space-between;
            color:#555;
        }

        .total{
            font-size:15px;
            font-weight:bold;
        }

        .footer{
            text-align:center;
            font-size:11px;
            margin-top:10px;
        }

        .status{
            display:inline-block;
            border:1px solid #000;
            padding:2px 8px;
            margin-top:5px;
            font-weight:bold;
        }

        @media print{

        body{
            background:white;
            padding:0;
        }

        .receipt{
            width:58mm;
            max-width:58mm;
            box-shadow:none;
            padding:5px;
        }

        }

        @page{
            size:58mm auto;
            margin:0;
        }
    </style>

</head>

<body>
    <div class="receipt">

        <div class="center">

            <div class="logo">QIEOS</div>

            <div class="receipt-title">
                STRUK PEMBELIAN
            </div>

        </div>

        <hr>

        <div class="info row">
            <span>Order ID.</span>
            <span><?= $order['code'] ?></span>
        </div>

        <div class="info row">
            <span>Tanggal</span>
            <span><?= date('d/m/Y', strtotime($order['tanggal'])) ?></span>
        </div>

        <div class="info row">
            <span>Kasir</span>
            <span><?= $user['fullname'] ?></span>
        </div>

        <hr>

        <?php while($d=mysqli_fetch_assoc($details)){ ?>

            <div class="item">

                <div class="item-name">
                    <?= ucwords(strtolower($d['product_name'])) ?>
                </div>

                <div class="item-detail">

                    <span>
                        <?= $d['qty'] ?> × Rp <?= number_format($d['price']) ?>
                    </span>

                    <span>
                        Rp <?= number_format($d['subtotal']) ?>
                    </span>

                </div>

            </div>

        <?php } ?>

        <hr>

        <div class="row">
            <span>Subtotal</span>
            <span>Rp <?= number_format($order['total']) ?></span>
        </div>

        <div class="row">
            <span>Diskon</span>
            <span>Rp 0</span>
        </div>

        <div class="row">
            <span>Pajak</span>
            <span>Rp 0</span>
        </div>

        <hr>

        <div class="row total">
            <span>TOTAL</span>
            <span>Rp <?= number_format($order['total']) ?></span>
        </div>

        <hr>

        <div class="center">

            <div class="status">
                LUNAS
            </div>

        </div>

        <div class="footer">

            Terima kasih atas pembelian Anda.<br>

            Simpan struk ini sebagai bukti transaksi.

        </div>

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