<?php
include '../../sessions/session.php';
require_once '../../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if($id <= 0){
    http_response_code(400);
    exit('ID form pembelian tidak valid.');
}

$purchaseQuery = mysqli_query($conn, "
    SELECT form, receipt, date
    FROM purchases
    WHERE id = '$id'
      AND deleted_at IS NULL
    LIMIT 1
");
if(!$purchaseQuery){
    error_log('[QIEOS Purchase PDF] Gagal membaca pembelian: ' . mysqli_error($conn));
    http_response_code(500);
    exit('Data pembelian tidak dapat dimuat.');
}
$purchase = mysqli_fetch_assoc($purchaseQuery);
if(!$purchase){
    http_response_code(404);
    exit('Form pembelian tidak ditemukan.');
}

$itemsQuery = mysqli_query($conn, "
    SELECT
        COALESCE(p.name, '') AS name,
        s.name AS supplier,
        pi.invoice_no,
        pi.qty_buy AS qty,
        pi.unit_buy AS unit,
        pi.price_buy AS price
    FROM purchase_items pi
    LEFT JOIN products p ON p.id = pi.product_id
    LEFT JOIN suppliers s ON s.id = pi.supplier_id
    WHERE pi.purchase_id = '$id'
      AND pi.deleted_at IS NULL
    ORDER BY pi.id ASC
");
if(!$itemsQuery){
    error_log('[QIEOS Purchase PDF] Gagal membaca item pembelian: ' . mysqli_error($conn));
    http_response_code(500);
    exit('Detail pembelian tidak dapat dimuat.');
}

function purchasePdfEscape($value){
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function purchasePdfDate($value){
    if(!$value || $value === '0000-00-00') return '-';
    $timestamp = strtotime($value);
    if($timestamp === false) return '-';
    $months = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    return date('d', $timestamp) . ' ' . $months[(int)date('n', $timestamp)] . ' ' . date('Y', $timestamp);
}

$hasReceipt = !empty($purchase['receipt']);
$documentTitle = $hasReceipt ? 'Form Receipt' : 'Daftar Belanja';
$printedAt = new DateTimeImmutable();
if(isset($_GET['printed_at'])){
    try{
        $printedAt = new DateTimeImmutable($_GET['printed_at']);
    }catch(Exception $error){
        http_response_code(400);
        exit('Waktu cetak tidak valid.');
    }
}
$printedAtLabel = purchasePdfDate($printedAt->format('Y-m-d')) . ' ' . $printedAt->format('H:i');
$total = 0;
$rowNumber = 0;

ob_start();
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title><?= purchasePdfEscape($documentTitle . ' ' . ($purchase['receipt'] ?: $purchase['form'])) ?></title>
    <style>
        @page{size:A4 portrait;margin:12mm}
        *{box-sizing:border-box}
        body{margin:0;color:#172033;font-family:"DejaVu Sans",sans-serif;font-size:9px}
        .header{width:100%;margin-bottom:16px;padding-bottom:12px;border-bottom:3px solid #312e81}
        .header-table,.meta-table,.data-table,.sign-table{width:100%;border-collapse:collapse}
        .header-brand{color:#312e81;font-size:14px;font-weight:bold;letter-spacing:.5px}
        .title{margin-top:8px;color:#172033;font-size:18px;font-weight:bold}
        .subtitle{margin-top:4px;color:#64748b;font-size:9px}
        .meta-table{margin-bottom:14px}
        .meta-table td{width:25%;padding:7px 9px;border:1px solid #dbe3f0;background:#f8fafc;vertical-align:top}
        .meta-label{color:#64748b;font-size:7px;font-weight:bold;text-transform:uppercase;letter-spacing:.6px}
        .meta-value{margin-top:4px;color:#172033;font-size:9px;font-weight:bold}
        .data-table{table-layout:fixed}
        .data-table th{padding:7px 5px;border:1px solid #312e81;background:#312e81;color:#fff;text-align:center;font-size:7px;text-transform:uppercase}
        .data-table td{padding:6px 5px;border:1px solid #cbd5e1;vertical-align:middle}
        .center{text-align:center}.right{text-align:right}.nowrap{white-space:nowrap}
        .manual{min-width:60px}
        .check{width:30px;padding:0;text-align:center;vertical-align:middle}
        .check-box{display:block;width:12px;height:12px;margin:0 auto;border:1px solid #64748b}
        .total-row td{background:#f1f5f9;font-weight:bold}
        .empty-row td{padding:18px;text-align:center;color:#64748b}
        .sign-table{margin-top:24px;table-layout:fixed}
        .sign-cell{width:33.33%;padding:0 10px;vertical-align:top;text-align:center}
        .sign-card{height:125px;padding-bottom:8px;border:1px solid #cbd5e1}
        .sign-role{padding:9px 5px;background:#f8fafc;color:#334155;font-size:8px;font-weight:bold;text-transform:uppercase;letter-spacing:.5px}
        .sign-space{height:64px}
        .sign-line{margin:0 12px;padding-top:7px;border-top:1px solid #64748b;color:#475569;font-size:7px;line-height:1.4}
        .printed-note{margin-top:13px;color:#64748b;text-align:right;font-size:7px}
        thead{display:table-header-group}
        tr,.sign-card{page-break-inside:avoid}
    </style>
</head>
<body>
    <div class="header">
        <table class="header-table">
            <tr>
                <td>
                    <div class="header-brand">PT. SELARASGRIYA SARANA UTAMA</div>
                    <div class="title"><?= purchasePdfEscape($documentTitle) ?></div>
                    <div class="subtitle">Dokumen pembelian dan verifikasi barang</div>
                </td>
            </tr>
        </table>
    </div>

    <table class="meta-table">
        <tr>
            <td><div class="meta-label">No. Form</div><div class="meta-value"><?= purchasePdfEscape($purchase['form']) ?></div></td>
            <td><div class="meta-label">No. Receipt</div><div class="meta-value"><?= purchasePdfEscape($purchase['receipt'] ?: '-') ?></div></td>
            <td><div class="meta-label">Tanggal Form</div><div class="meta-value"><?= purchasePdfEscape(purchasePdfDate($purchase['date'])) ?></div></td>
            <td><div class="meta-label">Dicetak pada</div><div class="meta-value"><?= purchasePdfEscape($printedAtLabel) ?></div></td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th>No</th><th>Nama Barang / Produk</th><th>Supplier</th><th>No Invoice</th>
                <th>Qty</th>
                <?php if(!$hasReceipt): ?><th>Qty Revisi</th><?php endif; ?>
                <th>Satuan</th><th>Total Harga</th>
                <?php if(!$hasReceipt): ?><th>Total Harga Revisi</th><th class="check">Check</th><?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php while($item = mysqli_fetch_assoc($itemsQuery)): $rowNumber++; $total += (float)$item['price']; ?>
            <tr>
                <td class="center"><?= $rowNumber ?></td>
                <td><?= purchasePdfEscape($item['name']) ?></td>
                <td><?= purchasePdfEscape($item['supplier'] ?: '-') ?></td>
                <td><?= purchasePdfEscape($item['invoice_no'] ?: '-') ?></td>
                <td class="center"><?= purchasePdfEscape($item['qty']) ?></td>
                <?php if(!$hasReceipt): ?><td class="manual"></td><?php endif; ?>
                <td class="center"><?= purchasePdfEscape($item['unit']) ?></td>
                <td class="right nowrap">Rp <?= number_format((float)$item['price'], 0, ',', '.') ?></td>
                <?php if(!$hasReceipt): ?><td class="manual"></td><td class="check"><span class="check-box"></span></td><?php endif; ?>
            </tr>
            <?php endwhile; ?>
            <?php if($rowNumber === 0): ?>
            <tr class="empty-row"><td colspan="<?= $hasReceipt ? 7 : 10 ?>">Tidak ada item pada dokumen ini.</td></tr>
            <?php endif; ?>
            <tr class="total-row">
                <td colspan="<?= $hasReceipt ? 6 : 7 ?>" class="right">Total Harga Keseluruhan</td>
                <td class="right nowrap">Rp <?= number_format($total, 0, ',', '.') ?></td>
                <?php if(!$hasReceipt): ?><td></td><td></td><?php endif; ?>
            </tr>
        </tbody>
    </table>

    <table class="sign-table">
        <tr>
            <td class="sign-cell"><div class="sign-card"><div class="sign-role">Dibuat oleh</div><div class="sign-space"></div><div class="sign-line">Nama &amp; tanda tangan</div></div></td>
            <td class="sign-cell"><div class="sign-card"><div class="sign-role">Diperiksa oleh</div><div class="sign-space"></div><div class="sign-line">Nama &amp; tanda tangan</div></div></td>
            <td class="sign-cell"><div class="sign-card"><div class="sign-role">Diterima oleh</div><div class="sign-space"></div><div class="sign-line">Nama &amp; tanda tangan</div></div></td>
        </tr>
    </table>
    <div class="printed-note">Dokumen PDF melalui QIEOS POS Management System</div>
</body>
</html>
<?php
$html = ob_get_clean();

try{
    $options = new Options();
    $options->set('isRemoteEnabled', false);
    $options->set('defaultFont', 'DejaVu Sans');

    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html, 'UTF-8');
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    if(ob_get_length()) ob_end_clean();

    $number = $hasReceipt ? $purchase['receipt'] : $purchase['form'];
    $filename = preg_replace('/[^A-Za-z0-9._-]/', '_', $documentTitle . ' ' . $number) . '.pdf';
    $dompdf->stream($filename, ['Attachment' => false]);
}catch(Exception $error){
    if(ob_get_length()) ob_end_clean();
    error_log('[QIEOS Purchase PDF Error] ' . $error->getMessage());
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'PDF tidak dapat dibuat. Silakan coba kembali.';
}
