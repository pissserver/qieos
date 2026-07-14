<?php

include '../../../sessions/session.php';
require '../../../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$type       = $_GET['type'];
$tab        = $_GET['tab'];
$tenant_id  = $_GET['tenant_id'];
$first_date = $_GET['first_date'];
$last_date  = $_GET['last_date'];

$table = ($type == 'tenant')
    ? 'tenant_payments'
    : 'utility_payments';

$title = ($type == 'tenant')
    ? 'LAPORAN PEMBAYARAN TENANT'
    : 'LAPORAN PEMBAYARAN AIR & LISTRIK';

$title2 = ($type == 'tenant')
    ? 'Laporan Pembayaran Tenant'
    : 'Laporan Pembayaran Air & Listrik';

$where = [];

if (!empty($first_date) && !empty($last_date)) {
    $where[] = "DATE(p.payment_date) BETWEEN '$first_date' AND '$last_date'";
}

if ($tab == "single" && !empty($tenant_id)) {
    $where[] = "p.tenant_id = '$tenant_id'";
}

$whereSql = "";

if (count($where) > 0) {
    $whereSql = "WHERE " . implode(" AND ", $where);
}

$query = mysqli_query($conn, "
SELECT
    p.*,
    t.tenant_name
FROM $table p
INNER JOIN tenants t
ON t.id = p.tenant_id
$whereSql
ORDER BY p.payment_date DESC
");

$total = 0;

ob_start();
?>

<!DOCTYPE html>
<html>

<head>

<title><?= $title2 ?> - Qieos</title>
<meta charset="utf-8">

<style>

body{
    font-family: DejaVu Sans;
    font-size:12px;
    color:#222;
}

.header{
    text-align:center;
    margin-bottom:25px;
}

.company{
    font-size:22px;
    font-weight:bold;
}

.address{
    font-size:11px;
    color:#666;
}

.title{
    margin-top:15px;
    font-size:17px;
    font-weight:bold;
}

.period{
    margin-top:5px;
    font-size:12px;
}

.line{
    border-top:2px solid #000;
    margin:18px 0;
}

table{
    width:100%;
    border-collapse: separate;
    border-spacing:0;
}

th{
    background:#3f51b5;
    color:white;
    border:1px solid #999;
    padding:8px;
}

td{
    border:1px solid #999;
    padding:8px;
}

.center{
    text-align:center;
}

.right{
    text-align:right;
}

tfoot td{
    font-weight:bold;
    background:#f5f5f5;
}

.footer{
    margin-top:35px;
    text-align:right;
    font-size:11px;
}

.badge{

    display:inline-block;
    padding:3px 10px;
    background:#28a745;
    color:white;
    border-radius:4px;

}
</style>

</head>

<body>

<div class="header">

    <div class="company">
        PT. SELARASGRIYA SARANA UTAMA
    </div>

    <div class="address">
        Pasar Induk Surabaya Sidotopo
    </div>

    <div class="title">
        <?= $title ?>
    </div>

    <div class="period">

        Periode :

        <?= date('d M Y', strtotime($first_date)); ?>

        s/d

        <?= date('d M Y', strtotime($last_date)); ?>

    </div>

</div>

<div class="line"></div>

<table>

<thead>

<tr>

<th class="center" width="6%">No</th>
<th class="center">Nama Tenant</th>
<th class="center" width="18%">Tanggal</th>
<th class="center" width="20%">Jumlah</th>
<th class="center" width="15%">Status</th>

</tr>

</thead>

<tbody>

<?php

$no = 1;

if(mysqli_num_rows($query)==0){

?>

<tr>

<td colspan="5" class="center">

Tidak ada data.

</td>

</tr>

<?php

}else{

while($row = mysqli_fetch_assoc($query)){

$total += $row['cost_payment'];

?>

<tr>

<td class="center">
<?= $no++ ?>
</td>

<td>
<?= htmlspecialchars($row['tenant_name']) ?>
</td>

<td class="center">
<?= date('d M Y',strtotime($row['payment_date'])) ?>
</td>

<td class="right">
Rp <?= number_format($row['cost_payment'],0,',','.') ?>
</td>

<td class="center">

<?php
if($row['status']=="paid"){
    echo '<span>Lunas</span>';
}else{
    echo ucfirst($row['status']);
}
?>

</td>

</tr>

<?php

}

}

?>

</tbody>

<tfoot>

<tr>

<td colspan="3" class="right">

TOTAL PEMBAYARAN

</td>

<td class="right">

Rp <?= number_format($total,0,',','.') ?>

</td>

<td></td>

</tr>

</tfoot>

</table>

<div class="footer">

Dicetak pada :

<?= date('d F Y H:i'); ?>

</div>

</body>

</html>

<?php

$html = ob_get_clean();

$options = new Options();
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);

$dompdf->loadHtml($html);

$dompdf->setPaper('A4','potrait');

$dompdf->render();

if (ob_get_length()) {
    ob_end_clean();
}

$dompdf->stream(

    $title2 . " - " . date('d M Y', strtotime($first_date)) . " s.d. " . date('d M Y', strtotime($last_date)) . ".pdf",

    [
        "Attachment" => false
    ]

);