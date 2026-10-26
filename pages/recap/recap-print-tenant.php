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
    p.*, t.tenant_name, u.fullname 
FROM $table p
LEFT JOIN users u ON p.staff_id = u.id
LEFT JOIN tenants t ON p.tenant_id = t.id
WHERE DATE(payment_date)
BETWEEN '$first_date' AND '$last_date'
ORDER BY p.payment_date ASC, t.tenant_name ASC
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

<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/recap-print-tenant.css?v=<?php echo filemtime(__DIR__ . '/../../css/pages/recap-print-tenant.css'); ?>">

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

        <hr>

        <!-- RINGKASAN DI ATAS: angka kunci langsung terlihat -->
        <div class="summary">
            <div class="row">
                <span>Total Pembayaran</span>
                <span><?= number_format($totalPayment,0,',','.'); ?></span>
            </div>

            <div class="row">
                <span>Pembayaran</span>
                <span>Rp <?= number_format($grandTotal,0,',','.'); ?></span>
            </div>

            <div class="row">
                <span>Rata-rata</span>
                <span>Rp <?= number_format($avg,0,',','.'); ?></span>
            </div>
        </div>

        <hr>

        <!-- TABEL RINGKAS: satu pembayaran = satu baris -->
        <div class="tbl">
            <div class="tbl-row tbl-head">
                <span class="tbl-c-no">No</span>
                <span class="tbl-c-name">Tenant</span>
                <span class="tbl-c-staff">Kasir</span>
                <span class="tbl-c-amount">Total</span>
            </div>

            <?php $no = 1; foreach($data as $d){ ?>
                <div class="tbl-row<?= ($no % 2) ? ' zebra' : '' ?>">
                    <span class="tbl-c-no"><?= $no++; ?></span>
                    <span class="tbl-c-name" title="<?= htmlspecialchars($d['tenant_name'] ?: '', ENT_QUOTES); ?>"><?= htmlspecialchars($d['tenant_name'] ?: '-'); ?></span>
                    <span class="tbl-c-staff"><?= htmlspecialchars($d['fullname'] ?: '-'); ?></span>
                    <span class="tbl-c-amount"><?= number_format($d['cost_payment'],0,',','.'); ?></span>
                </div>
            <?php } ?>
        </div>

        <div class="footer">

            Laporan dibuat oleh<br>
            <strong>QIEOS Point Of Sales</strong>

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
