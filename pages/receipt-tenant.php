<?php
include '../sessions/session.php';

$payment_id = $_GET['payment_id'];
$type = $_GET['type'];

if ($type === 'utility') {
    $table = 'utility_payments';
    $title = "PEMBAYARAN AIR & LISTRIK";
} else {
    $table = 'tenant_payments';
    $title = "PEMBAYARAN TENANT";
}

$details = mysqli_query($conn, "
    SELECT d.*, t.tenant_name, u.fullname
    FROM $table d
    LEFT JOIN users u ON d.staff_id = u.id 
    LEFT JOIN tenants t ON d.tenant_id = t.id
    WHERE d.id = '$payment_id'
")->fetch_assoc();
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Struk Pembayaran - Qieos</title>
    <link
        rel="icon"
        sizes="120x120"
        href="../assets/img/brand/qieos2.png" />

    <style>
        body{
            margin:0;
            padding:15px;
            background:#efefef;
            font-family: monospace;
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

        .logo{
            font-size:22px;
            font-weight:bold;
            letter-spacing:2px;
        }

        .title{
            font-size:13px;
            font-weight:bold;
            margin-top:3px;
        }

        .small{
            font-size:11px;
            color:#555;
        }

        hr{
            border:none;
            border-top:1px dashed #000;
            margin:10px 0;
        }

        .row{
            display:flex;
            justify-content:space-between;
            margin:2px 0;
        }

        .label{
            color:#555;
        }

        .value{
            text-align:right;
            font-weight:bold;
        }

        .total{
            font-size:15px;
            font-weight:bold;
        }

        .center{
            text-align:center;
        }

        .footer{
            margin-top:12px;
            font-size:11px;
            text-align:center;
        }

        .status{
            display:inline-block;
            border:1px solid #000;
            padding:2px 8px;
            font-weight:bold;
            margin-top:5px;
        }

        @media print{

        body{
            background:white;
            padding:0;
        }

        .receipt{
            width:58mm;
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
            <div class="title"><?= $title ?></div>
        </div>

        <hr>

        <div class="row">
            <span class="label">Tanggal</span>
            <span><?= date('d/m/Y', strtotime($details['payment_date'])) ?></span>
        </div>

        <div class="row">
            <span class="label">Kasir</span>
            <span><?= $details['fullname'] ?></span>
        </div>

        <div class="row">
            <span class="label">Tenant</span>
            <span><?= $details['tenant_name'] ?></span>
        </div>

        <hr>

        <div class="row">
            <span>Tagihan Tenant</span>
            <span>Rp <?= number_format($details['cost_payment']) ?></span>
        </div>

        <hr>

        <div class="row total">
            <span>TOTAL</span>
            <span>Rp <?= number_format($details['cost_payment']) ?></span>
        </div>

        <hr>

        <div class="center">
            <div class="status">LUNAS</div>
        </div>

        <div class="footer">
            Terima kasih telah melakukan pembayaran.<br>
            Simpan struk ini sebagai bukti pembayaran.
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