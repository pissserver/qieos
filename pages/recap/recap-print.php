<?php

include '../../sessions/session.php';

$first_date = $_GET['first_date'] ? $_GET['first_date'] : '';
$last_date  = $_GET['last_date'] ? $_GET['last_date'] : '';

$query = mysqli_query($conn,"
SELECT
    o.*,
    u.fullname
FROM orders o
LEFT JOIN users u
ON o.staff_id = u.id
WHERE DATE(o.tanggal)
BETWEEN '$first_date' AND '$last_date'
ORDER BY o.tanggal ASC,o.id ASC
");

$totalOrder = 0;
$grandTotal = 0;

$data = [];

while($row = mysqli_fetch_assoc($query)){

    $data[] = $row;
    $totalOrder++;
    $grandTotal += $row['total'];
    
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

<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/recap-print.css?v=<?php echo filemtime(__DIR__ . '/../../css/pages/recap-print.css'); ?>">

</head>

<body>

    <div class="receipt">

        <div class="center">

            <div class="logo">
                QIEOS
            </div>

            <div class="title">
                REKAP PENJUALAN
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
            <span>Total Order</span>
            <span><?= $totalOrder; ?></span>
        </div>

        <hr>

        <?php foreach($data as $d){ ?>
            <div class="item">

                <div class="code">
                    <?= $d['code']; ?>
                </div>

                <div class="date">
                    <?= date('d/m/Y',strtotime($d['tanggal'])); ?>
                </div>

                <div class="staff">
                    <?= $d['fullname']; ?>
                </div>

                <div class="amount">
                    Rp <?= number_format($d['total'],0,',','.'); ?>
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
