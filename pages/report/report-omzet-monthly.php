<?php
error_reporting(0);
ob_start();
include '../../html2pdf/html2pdf.class.php';
include '../../script/connection.php';

$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];

$query = mysqli_query($conn, "
    SELECT 
        *
    FROM orders
    WHERE DATE(tanggal) BETWEEN '$start_date' AND '$end_date'
    AND status_payment != 'cancelled'
    ORDER BY id DESC
");

if (!$query) {
    die(mysqli_error($conn));
}

$total_payment = 0;

ob_start();
?>

<style>
body { font-family: Arial; font-size: 11px; }
.header { text-align: center; margin-bottom: 10px; }
.print-date { text-align: right; font-size: 10px; margin-bottom: 5px; }
h3 { margin: 0; margin-top: 30px; }
h4 { margin: 3px 0 10px 0; font-weight: normal; }
table { width: 100%; border-collapse: collapse; margin-top: 8px; table-layout: fixed; }
th, td { border: 1px solid #000; padding: 5px; }
th { background-color: #f0f0f0; text-align: center; }
.center { text-align: center; }
.right { text-align: right; }
.signature { width: 100%; margin-top: 40px; }
.signature td { border: none; text-align: center; width: 50%; }
.footer { text-align: center; font-size: 10px; }
</style>

<page>

<div class="print-date">
    Dicetak: <?= date('d M Y'); ?>
</div>

<div class="header">
    <h3>LAPORAN OMZET BULANAN</h3>
    <h4>SISTEM PENJUALAN - CARTIFY</h4>
</div>

<p>
    Tanggal:
    <b><?= date("d M Y", strtotime($start_date)); ?> - <?= date("d M Y", strtotime($end_date)); ?></b>
</p>

<table>
<tr>
    <th width="15%">No</th>
    <th width="25%">Nama Pelanggan</th>
    <th width="20%">Status Pembayaran</th>
    <th width="40%">Total Pembayaran (Rp)</th>
</tr>

<?php
$no = 1;
$line = 0;

while ($row = mysqli_fetch_assoc($query)) {
    $line++;
    $total_payment += $row['total'];

?>
<tr>
    <td class="center"><?= $no++; ?></td>
    <td><?= $row['customer_name']; ?></td>
    <td class="center"><?= $row['status_payment'] == 'paid' ? 'Terbayar' : 'Belum Terbayar'; ?></td>
    <td class="right"><?= number_format($row['total'],0,',','.'); ?></td>
</tr>
<?php } ?>

<tr>
    <td colspan="3" class="center"><strong>TOTAL</strong></td>
    <td colspan="2" class="right">
        <strong>Rp <?= number_format($total_payment,0,',','.'); ?></strong>
    </td>
</tr>
</table>

<page_footer>
    <div class="footer">
        Halaman [[page_cu]] / [[page_nb]]
    </div>
</page_footer>

</page>

<?php
$html = ob_get_clean();

try {
    $pdf = new HTML2PDF('P', 'A4', 'en');
    $pdf->setDefaultFont('Arial');
    $pdf->writeHTML($html);
    $pdf->Output('laporan_omzet_bulanan.pdf', 'I');
} catch (HTML2PDF_exception $e) {
    echo $e;
}
exit;
