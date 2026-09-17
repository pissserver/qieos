<?php

include '../../sessions/session.php';
require '../../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

function trPdfDateId($date)
{
    if (empty($date) || $date === '0000-00-00') {
        return '-';
    }
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    $t = strtotime($date);
    if ($t === false) {
        return '-';
    }
    return date('d', $t) . ' ' . $bulan[(int) date('n', $t)] . ' ' . date('Y', $t);
}

function trPdfEscape($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$request_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($request_id <= 0) {
    echo 'ID tidak valid';
    exit;
}

$qReq = mysqli_query($conn, "
    SELECT sr.*, COALESCE(NULLIF(u.fullname,''), u.username, '-') AS creator_name,
           u.role AS creator_role
    FROM stock_requests sr
    LEFT JOIN users u ON sr.created_by = u.id
    WHERE sr.id = $request_id
    LIMIT 1
");

if (!$qReq || mysqli_num_rows($qReq) == 0) {
    echo 'Request tidak ditemukan';
    exit;
}

$req = mysqli_fetch_assoc($qReq);

$qItems = mysqli_query($conn, "
    SELECT i.qty, p.name, p.code
    FROM stock_request_items i
    JOIN products p ON p.id = i.product_id
    WHERE i.request_id = $request_id
    ORDER BY i.id ASC
");

$items = [];
if ($qItems) {
    while ($it = mysqli_fetch_assoc($qItems)) {
        $items[] = $it;
    }
}

$reqCode     = trPdfEscape($req['code']);
$creatorName = trPdfEscape($req['creator_name']);
$creatorRole = isset($req['creator_role']) && $req['creator_role'] !== '' ? trPdfEscape(ucwords((string) $req['creator_role'])) : 'Staff';
$status      = trPdfEscape($req['status']);
$createdAt   = trPdfEscape(trPdfDateId($req['created_at']));
$printedAt   = trPdfEscape(trPdfDateId(date('Y-m-d'))) . ' · ' . date('H:i');
$printedBy   = isset($user['fullname']) && $user['fullname'] !== '' ? trPdfEscape($user['fullname']) : trPdfEscape($_SESSION['username']);
$printedRole = isset($user['role']) && $user['role'] !== '' ? trPdfEscape(ucwords((string) $user['role'])) : 'Staff';

$totalQty = 0;
foreach ($items as $it) {
    $totalQty += (int) $it['qty'];
}

ob_start();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Print Form Request - Qieos</title>
    <style>
        @page { margin: 22px 48px 50px 36px; }
        * { margin: 0; padding: 0; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10.5px; color: #0f172a; line-height: 1.45; }
        .wrap { width: 100%; }
        .brand-table { width: 100%; border-collapse: collapse; }
        .brand-cell { background: #1e1b4b; padding: 18px 20px 16px 20px; }
        .co-name { color: #ffffff; font-size: 14px; font-weight: bold; letter-spacing: 0.6px; }
        .co-sub { color: #c7d2fe; font-size: 8.5px; margin-top: 3px; letter-spacing: 0.2px; }
        .gold-bar { background: #c4a35a; height: 4px; font-size: 0; line-height: 0; }
        .title-block { padding: 16px 12px 12px 14px; }
        .report-kicker { font-size: 8px; font-weight: bold; letter-spacing: 1.6px; color: #6366f1; text-transform: uppercase; }
        .report-title { font-size: 16px; font-weight: bold; color: #1e1b4b; margin-top: 3px; letter-spacing: 0.3px; }
        .meta-table { width: 100%; border-collapse: collapse; margin: 4px 0 14px 0; }
        .meta-table td { width: 33.33%; background: #f8fafc; border: 1px solid #e2e8f0; padding: 9px 12px; vertical-align: top; }
        .meta-label { font-size: 7.5px; font-weight: bold; letter-spacing: 1px; color: #94a3b8; text-transform: uppercase; }
        .meta-value { font-size: 10.5px; font-weight: bold; color: #1e1b4b; margin-top: 2px; }
        .data-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .data-table thead { display: table-header-group; }
        .data-table th { background: #1e1b4b; color: #ffffff; font-size: 8px; font-weight: bold; letter-spacing: 0.8px; text-transform: uppercase; padding: 8px 10px; border: 1px solid #1e1b4b; text-align: center; overflow: hidden; }
        .data-table td { padding: 7px 10px; border: 1px solid #e2e8f0; font-size: 9.5px; color: #1e293b; vertical-align: middle; overflow: hidden; }
        .w5  { width: 5%; }
        .w10 { width: 10%; }
        .w35 { width: 35%; }
        .w25 { width: 25%; }
        .w20 { width: 20%; }
        .w15 { width: 15%; }
        .row-a { background: #ffffff; }
        .row-b { background: #f8fafc; }
        .c { text-align: center; }
        .r { text-align: right; }
        .l { text-align: left; }
        .badge { display: inline-block; font-size: 8px; font-weight: bold; letter-spacing: 0.4px; padding: 3px 10px; border-radius: 10px; text-align: center; }
        .badge-pending { background: #fef3c7; color: #b45309; }
        .badge-approved { background: #d1fae5; color: #047857; }
        .badge-rejected { background: #fee2e2; color: #b91c1c; }
        .total-row td { background: #1e1b4b; color: #ffffff; font-weight: bold; font-size: 10px; padding: 9px 8px; border: 1px solid #1e1b4b; }
        .sign-table { width: 100%; border-collapse: collapse; margin-top: 28px; }
        .sign-box { width: 46%; text-align: center; vertical-align: top; font-size: 9px; color: #64748b; }
        .sign-title { font-size: 8px; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; color: #94a3b8; margin-bottom: 4px; }
        .sign-line { margin-top: 48px; border-top: 1px solid #cbd5e1; padding-top: 6px; color: #1e1b4b; font-weight: bold; font-size: 9.5px; }
        .sign-role { font-size: 8px; color: #64748b; font-weight: 500; }
        .foot { margin-top: 18px; border-top: 1px solid #e2e8f0; padding: 8px 0 0 14px; font-size: 8px; color: #94a3b8; }
    </style>
</head>
<body>
<div class="wrap">

    <table class="brand-table" cellspacing="0" cellpadding="0">
        <tr>
            <td class="brand-cell">
                <div class="co-name">PT. SELARASGRIYA SARANA UTAMA</div>
                <div class="co-sub">Pasar Induk Surabaya Sidotopo &nbsp;·&nbsp; QIEOS POS Management System</div>
            </td>
        </tr>
        <tr><td class="gold-bar">&nbsp;</td></tr>
    </table>

    <div class="title-block">
        <div class="report-kicker">Request Stok</div>
        <div class="report-title">FORM REQUEST STOK GUDANG</div>
    </div>

    <table class="meta-table" cellspacing="0" cellpadding="0">
        <tr>
            <td>
                <div class="meta-label">No. Request</div>
                <div class="meta-value"><?= $reqCode ?></div>
            </td>
            <td>
                <div class="meta-label">Diajukan Oleh</div>
                <div class="meta-value"><?= $creatorName ?></div>
            </td>
            <td>
                <div class="meta-label">Dicetak</div>
                <div class="meta-value"><?= $printedAt ?></div>
            </td>
        </tr>
    </table>

    <table class="data-table" cellspacing="0" cellpadding="0">
        <thead>
            <tr>
                <th class="w5">No</th>
                <th class="w35">Produk</th>
                <th class="w25">Kode</th>
                <th class="w20">Qty</th>
                <th class="w15">Status</th>
            </tr>
        </thead>
        <tbody>
        <?php if (count($items) === 0): ?>
            <tr>
                <td colspan="5" style="padding:28px 12px; text-align:center; color:#64748b; font-size:11px; background:#f8fafc;">
                    Tidak ada item dalam request ini.
                </td>
            </tr>
        <?php else: ?>
            <?php foreach ($items as $i => $it): ?>
            <tr class="<?= $i % 2 === 0 ? 'row-a' : 'row-b' ?>">
                <td class="c w5"><?= $i + 1 ?></td>
                <td class="l w35"><b><?= trPdfEscape($it['name']) ?></b></td>
                <td class="c w25"><?= trPdfEscape($it['code']) ?></td>
                <td class="c w20"><?= (int) $it['qty'] ?> pcs</td>
                <?php if ($i === 0): ?>
                <td class="c w15" rowspan="<?= count($items) ?>">
                    <?php if ($status === 'pending'): ?>
                        <span class="badge badge-pending">Pending</span>
                    <?php elseif ($status === 'approved'): ?>
                        <span class="badge badge-approved">Approved</span>
                    <?php else: ?>
                        <span class="badge badge-rejected">Rejected</span>
                    <?php endif; ?>
                </td>
                <?php endif; ?>
            </tr>
            <?php endforeach; ?>
            <tr class="total-row">
                <td colspan="3" class="r">TOTAL QTY</td>
                <td class="c"><?= $totalQty ?> pcs</td>
                <td></td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>

    <table class="sign-table" cellspacing="0" cellpadding="0">
        <tr>
            <td class="sign-box">
                <div class="sign-title">Diajukan Oleh</div>
                <div><?= $createdAt ?></div>
                <div class="sign-line"><?= $creatorName ?></div>
                <div class="sign-role"><?= $creatorRole ?></div>
            </td>
            <td width="8%"></td>
            <td class="sign-box">
                <div class="sign-title">Mengetahui</div>
                <div>Surabaya, <?= trPdfDateId(date('Y-m-d')) ?></div>
                <div class="sign-line">________________</div>
                <div class="sign-role">Pimpinan / Penanggung Jawab</div>
            </td>
        </tr>
    </table>

    <div class="foot">
        Dokumen ini dicetak otomatis melalui QIEOS POS Management System · PT. Selarasgriya Sarana Utama · Bersifat resmi dan dapat digunakan sebagai arsip laporan.
    </div>

</div>
</body>
</html>
<?php

$html = ob_get_clean();

try {
    $options = new Options();
    $options->set('isRemoteEnabled', true);
    $options->set('isHtml5ParserEnabled', true);
    $options->set('defaultFont', 'DejaVu Sans');
    $projectRoot = realpath(__DIR__ . '/../../..');
    if ($projectRoot) {
        $options->setChroot($projectRoot);
    }

    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html, 'UTF-8');
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    $canvas = $dompdf->getCanvas();
    $font   = $dompdf->getFontMetrics()->getFont('DejaVu Sans', 'normal');
    $canvas->page_text(430, 812, "Halaman {PAGE_NUM} dari {PAGE_COUNT}", $font, 8, [0.58, 0.63, 0.72]);

    if (ob_get_length()) {
        ob_end_clean();
    }

    $filename = 'Request Stok ' . $req['code'] . ' - ' . trPdfDateId(date('Y-m-d', strtotime($req['created_at']))) . '.pdf';

    $dompdf->stream($filename, [
        'Attachment' => false
    ]);

} catch (Exception $e) {
    if (ob_get_length()) {
        ob_end_clean();
    }
    error_log('[QIEOS PDF Error] ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
    header('Content-Type: text/html; charset=utf-8');
    echo '<h3>Error Generating PDF</h3>';
    echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
    echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    exit;
}