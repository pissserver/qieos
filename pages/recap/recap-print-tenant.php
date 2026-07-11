<?php

include '../../sessions/session.php';

$first_date = $_GET['first_date'] ? $_GET['first_date'] : '';
$last_date  = $_GET['last_date'] ? $_GET['last_date'] : '';
$type  = $_GET['type'] ? $_GET['type'] : '';

if ($type === 'utility') {
    $table = 'utility_payments';
    $title = 'AIR & LISTRIK';
} elseif ($type === 'tenant') {
    $table = 'tenant_payments';
    $title = 'TENANT';
}

$query = mysqli_query($conn,"
SELECT
    p.*, t.tenant_name 
FROM $table p
LEFT JOIN tenants t ON p.tenant_id = t.id
WHERE DATE(payment_date)
BETWEEN '$first_date' AND '$last_date'
ORDER BY payment_date ASC, id ASC
");

$totalPayment = 0;
$grandTotal = 0;

$data = [];

while($row = mysqli_fetch_assoc($query)){

    $data[] = $row;
    $totalPayment++;
    $grandTotal += $row['cost_payment'];
    
}

$avg = $totalPayment ? ($grandTotal / $totalPayment) : 0;

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <title>Print Rekap Pembayaran - Qieos</title>
    <link
        rel="icon"
        sizes="120x120"
        href="../../assets/img/brand/qieos2.png" />

<style>

    body{
        margin:0;
        padding:20px;
        background:#ececec;
        font-family:monospace;
        display:flex;
        justify-content:center;
    }

    .receipt{

        width:300px;
        background:#fff;
        padding:14px;
        box-shadow:0 5px 20px rgba(0,0,0,.15);

    }

    .logo{

        font-size:22px;
        font-weight:bold;
        letter-spacing:3px;

    }

    .title{

        font-size:13px;
        font-weight:bold;
        margin-top:3px;

    }

    .center{

        text-align:center;

    }

    .small{

        font-size:11px;
        color:#555;

    }

    hr{

        border:none;
        border-top:1px dashed #000;
        margin:8px 0;

    }

    .item{

        margin:8px 0;

    }

    .code{

        font-weight:bold;

    }

    .date{

        font-size:10px;
        color:#555;

    }

    .staff{

        font-size:11px;

    }

    .amount{

        text-align:right;
        font-weight:bold;
        margin-top:3px;

    }

    .row{

        display:flex;
        justify-content:space-between;
        margin:3px 0;

    }

    .grand{

        font-size:15px;
        font-weight:bold;

    }

    .footer{

        text-align:center;
        font-size:11px;
        margin-top:10px;

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

            <div class="logo">
                QIEOS
            </div>

            <div class="title">
                REKAP PEMBAYARAN <?= $title ?>
            </div>

            <div class="small">
                <?= date('d M Y',strtotime($first_date)); ?>
                -
                <?= date('d M Y',strtotime($last_date)); ?>
            </div>

        </div>

        <hr>

        <div class="row">
            <span>Dicetak</span>
            <span><?= date('d/m/Y'); ?></span>
        </div>

        <div class="row">
            <span>Total Pembayaran</span>
            <span><?= $totalPayment; ?></span>
        </div>

        <hr>

        <?php foreach($data as $d){ ?>
            <div class="item">

                <div class="code">
                    <?= $d['tenant_name']; ?>
                </div>

                <div class="date">
                    <?= date('d/m/Y',strtotime($d['payment_date'])); ?>
                </div>

                <div class="amount">
                    Rp <?= number_format($d['cost_payment'],0,',','.'); ?>
                </div>

            </div>

            <hr>
        <?php } ?>

        <div class="summary">
            <div class="row">
                <span>Rata-rata</span>
                <span>Rp <?= number_format($avg,0,',','.'); ?></span>
            </div>

            <hr>

            <div class="row grand">
                <span>GRAND TOTAL</span>
                <span>Rp <?= number_format($grandTotal,0,',','.'); ?></span>
            </div>

            <hr>

            <div class="footer">

                Laporan dibuat oleh<br>
                <strong>QIEOS Point Of Sales</strong>

            </div>
        </div>
    
    </div>

    <script>

        window.onload=function(){

            setTimeout(function(){

                window.print();

            },300);

        }

    </script>

</body>

</html>
