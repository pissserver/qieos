<?php

include '../../sessions/session.php';

$first_date = $_GET['first_date'] ? $_GET['first_date'] : '';
$last_date  = $_GET['last_date'] ? $_GET['last_date'] : '';

$query = mysqli_query($conn,"
SELECT *
FROM orders
WHERE DATE(tanggal) BETWEEN '$first_date' AND '$last_date'
ORDER BY tanggal ASC, id ASC
");

$totalOrder = 0;
$grandTotal = 0;

$data = [];

while($row = mysqli_fetch_assoc($query)){

    $data[] = $row;
    $totalOrder++;
    $grandTotal += $row['total'];

    $staff = mysqli_query($conn,"
        SELECT fullname FROM users WHERE id = ".$row['staff_id']."
    ")->fetch_assoc();

    
}

$avg = $totalOrder ? ($grandTotal / $totalOrder) : 0;

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <title>Print Rekap Penjualan - Qieos</title>
    <link
        rel="icon"
        sizes="120x120"
        href="../../assets/img/brand/qieos2.png" />

<style>

    *{
        margin:0;
        padding:0;
        box-sizing:border-box;
        font-family:monospace;
    }

    body{

        background:#efefef;
        display:flex;
        justify-content:center;
        padding:15px;

    }

    .receipt{

        width:58mm;
        background:#fff;
        padding:8px;

    }

    .center{

        text-align:center;

    }

    .center h2{

        font-size:18px;
        margin-bottom:2px;

    }

    .center p{

        font-size:11px;
        margin:2px 0;

    }

    hr{

        border:none;
        border-top:1px dashed #000;
        margin:8px 0;

    }

    .row{

        display:flex;
        justify-content:space-between;
        font-size:11px;
        margin:2px 0;

    }

    .item{

        margin-bottom:8px;

    }

    .item .code{

        font-weight:bold;
        font-size:12px;

    }

    .item .date{

        color:#444;
        font-size:10px;

    }

    .item .total{

        text-align:right;
        font-weight:bold;
        margin-top:2px;

    }

    .summary{

        font-size:11px;

    }

    .summary .row{

        margin:4px 0;

    }

    .grand{

        font-size:14px;
        font-weight:bold;

    }

    .footer{

        margin-top:10px;
        text-align:center;
        font-size:10px;

    }

    @media print{

    body{

        background:none;
        padding:0;
        display:block;

    }

    .receipt{

        width:58mm;
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

    <div class="center" style="margin-bottom:20px;">

    <h2>QIEOS</h2>

    <p>REKAP PENJUALAN</p>

    <p>
    <?= date('d/m/Y',strtotime($first_date)); ?>
    -
    <?= date('d/m/Y',strtotime($last_date)); ?>
    </p>

    </div>

    <hr>

    <div class="summary">

    <div class="row">
    <span>Dicetak</span>
    <span><?= date('d M Y'); ?></span>
    </div>

    </div>

    <hr>

    <?php if($totalOrder==0){ ?>

    <div class="center">

    Tidak ada transaksi

    </div>

    <?php } ?>

    <?php foreach($data as $d){ ?>

    <div class="item">

    <div class="code">
    <?= $d['code']; ?>
    </div>

    <div class="date">
    <?= date('d M Y',strtotime($d['tanggal'])); ?>
    </div>

    <div class="date">
    <?= $staff['fullname']; ?>
    </div>

    <div class="total">

    Rp <?= number_format($d['total'],0,',','.'); ?>

    </div>

    </div>

    <hr>

    <?php } ?>

    <div class="summary">

    <div class="row">

    <span>Total Order</span>

    <span><?= $totalOrder; ?> Order</span>

    </div>

    <div class="row">

    <span>Rata-rata</span>

    <span>

    Rp <?= number_format($avg,0,',','.'); ?>

    </span>

    </div>

    <hr>

    <div class="row grand">

    <span>GRAND TOTAL</span>

    <span>

    Rp <?= number_format($grandTotal,0,',','.'); ?>

    </span>

    </div>

    </div>

    <hr>

    <div class="footer">

    Terima Kasih

    <br>

    QIEOS Point Of Sales

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
