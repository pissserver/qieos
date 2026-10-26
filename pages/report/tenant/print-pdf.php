<?php

include '../../../sessions/session.php';
require '../../../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

function pdfDateId($date)
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

function pdfEscape($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$type       = isset($_GET['type']) ? $_GET['type'] : 'tenant';
$tab        = isset($_GET['tab']) ? $_GET['tab'] : 'all';
$tenant_id  = isset($_GET['tenant_id']) ? $_GET['tenant_id'] : '';
$first_date = isset($_GET['first_date']) ? $_GET['first_date'] : '';
$last_date  = isset($_GET['last_date']) ? $_GET['last_date'] : '';

$isUtility = ($type === 'utility');
$isSingle  = ($tab === 'single' && $tenant_id !== '');

$table = $isUtility ? 'utility_payments' : 'tenant_payments';

$title  = $isUtility ? 'LAPORAN PEMBAYARAN AIR & LISTRIK' : 'LAPORAN PEMBAYARAN TENANT';
$title2 = $isUtility ? 'Laporan Pembayaran Air & Listrik' : 'Laporan Pembayaran Tenant';
$kind   = $isUtility ? 'Air & Listrik' : 'Pembayaran Tenant';

$escFirst  = mysqli_real_escape_string($conn, $first_date);
$escLast   = mysqli_real_escape_string($conn, $last_date);
$escTenant = mysqli_real_escape_string($conn, $tenant_id);

$where = [];

if ($first_date !== '' && $last_date !== '') {
    $where[] = "DATE(p.payment_date) BETWEEN '$escFirst' AND '$escLast'";
}

if ($isSingle) {
    $where[] = "p.tenant_id = '$escTenant'";
}

$whereSql = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';

$query = mysqli_query($conn, "
    SELECT
        p.*,
        t.tenant_name
    FROM $table p
    INNER JOIN tenants t ON t.id = p.tenant_id
    $whereSql
    ORDER BY p.payment_date DESC, t.tenant_name ASC
");

$rows  = [];
$total = 0;
$paid  = 0;

if ($query) {
    while ($row = mysqli_fetch_assoc($query)) {
        $rows[] = $row;
        $total += (float) $row['cost_payment'];
        $rowStatus = isset($row['status']) ? $row['status'] : '';
        if ($rowStatus === 'paid') {
            $paid++;
        }
    }
}

$count      = count($rows);
$tenantName = $isSingle && $count > 0 ? $rows[0]['tenant_name'] : '';

if ($isSingle && $tenantName === '' && $tenant_id !== '') {
    $qName = mysqli_query($conn, "SELECT tenant_name FROM tenants WHERE id = '$escTenant' LIMIT 1");
    if ($qName && $nameRow = mysqli_fetch_assoc($qName)) {
        $tenantName = $nameRow['tenant_name'];
    }
}

$periodLabel = ($first_date !== '' && $last_date !== '')
    ? pdfDateId($first_date) . '  —  ' . pdfDateId($last_date)
    : 'Semua periode';

$printedAt = pdfDateId(date('Y-m-d')) . ' · ' . date('H:i') . ' WIB';
$printedBy = isset($user['fullname']) && $user['fullname'] !== ''
    ? $user['fullname']
    : (isset($_SESSION['username']) ? $_SESSION['username'] : 'QIEOS');

$printedRole = isset($user['role']) && $user['role'] !== ''
    ? ucwords((string) $user['role'])
    : (isset($_SESSION['role']) ? ucwords((string) $_SESSION['role']) : 'Staff');

$colspan = $isSingle ? 4 : 5;

ob_start();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?= pdfEscape($title2) ?> - Qieos</title>
    <style>
        @page {
            margin: 22px 48px 50px 36px;
        }

        * {
            margin: 0;
            padding: 0;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10.5px;
            color: #0f172a;
            line-height: 1.45;
        }

        .wrap {
            width: 100%;
        }

        .brand-table {
            width: 100%;
            border-collapse: collapse;
        }

        .brand-cell {
            background: #1e1b4b;
            padding: 18px 20px 16px 20px;
        }

        .co-name {
            color: #ffffff;
            font-size: 14px;
            font-weight: bold;
            letter-spacing: 0.6px;
        }

        .co-sub {
            color: #c7d2fe;
            font-size: 8.5px;
            margin-top: 3px;
            letter-spacing: 0.2px;
        }

        .gold-bar {
            background: #c4a35a;
            height: 4px;
            font-size: 0;
            line-height: 0;
        }

        .title-block {
            padding: 16px 12px 12px 14px;
        }

        .report-kicker {
            font-size: 8px;
            font-weight: bold;
            letter-spacing: 1.6px;
            color: #6366f1;
            text-transform: uppercase;
        }

        .report-title {
            font-size: 16px;
            font-weight: bold;
            color: #1e1b4b;
            margin-top: 3px;
            letter-spacing: 0.3px;
        }

        .report-period {
            font-size: 10px;
            color: #64748b;
            margin-top: 4px;
        }

        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin: 4px 0 14px 0;
        }

        .meta-table td {
            width: 33.33%;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 9px 12px;
            vertical-align: top;
        }

        .meta-label {
            font-size: 7.5px;
            font-weight: bold;
            letter-spacing: 1px;
            color: #94a3b8;
            text-transform: uppercase;
        }

        .meta-value {
            font-size: 10.5px;
            font-weight: bold;
            color: #1e1b4b;
            margin-top: 2px;
        }

        .kpi-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }

        .kpi-table td {
            width: 33.33%;
            padding: 11px 12px;
            border: 1px solid #e2e8f0;
            vertical-align: top;
        }

        .kpi-1 { background: #eef2ff; }
        .kpi-2 { background: #ecfdf5; }
        .kpi-3 { background: #fffbeb; }

        .kpi-label {
            font-size: 7.5px;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #64748b;
        }

        .kpi-value {
            font-size: 15px;
            font-weight: bold;
            color: #1e1b4b;
            margin-top: 3px;
        }

        .kpi-hint {
            font-size: 8px;
            color: #64748b;
            margin-top: 2px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table thead {
            display: table-header-group;
        }

        .data-table th {
            background: #1e1b4b;
            color: #ffffff;
            font-size: 8px;
            font-weight: bold;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            padding: 8px 10px;
            border: 1px solid #1e1b4b;
            text-align: center;
        }

        .data-table td {
            padding: 7px 10px;
            border: 1px solid #e2e8f0;
            font-size: 9.5px;
            color: #1e293b;
            vertical-align: middle;
        }

        .data-table th:last-child,
        .data-table td:last-child {
            padding-right: 14px;
        }

        .row-a { background: #ffffff; }
        .row-b { background: #f8fafc; }

        .c { text-align: center; }
        .r { text-align: right; }
        .l { text-align: left; }

        .tenant-name {
            font-weight: bold;
            color: #0f172a;
        }

        .badge {
            display: block;
            width: 54px;
            margin: 0 auto;
            font-size: 8px;
            font-weight: bold;
            letter-spacing: 0.4px;
            padding: 3px 0 5px 0;
            border-radius: 10px;
            text-align: center;
        }

        .badge-paid {
            background: #d1fae5;
            color: #047857;
        }

        .badge-other {
            background: #f1f5f9;
            color: #475569;
        }

        .empty-cell {
            padding: 28px 12px;
            text-align: center;
            color: #64748b;
            font-size: 11px;
            background: #f8fafc;
        }

        .total-row td {
            background: #1e1b4b;
            color: #ffffff;
            font-weight: bold;
            font-size: 10px;
            padding: 9px 8px;
            border: 1px solid #1e1b4b;
        }

        .total-amount {
            font-size: 12px;
            color: #c4a35a;
        }

        .sign-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 28px;
        }

        .sign-box {
            width: 46%;
            text-align: center;
            vertical-align: top;
            font-size: 9px;
            color: #64748b;
        }

        .sign-title {
            font-size: 8px;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #94a3b8;
            margin-bottom: 4px;
        }

        .sign-line {
            margin-top: 48px;
            border-top: 1px solid #cbd5e1;
            padding-top: 6px;
            color: #1e1b4b;
            font-weight: bold;
            font-size: 9.5px;
        }

        .sign-role {
            font-size: 8px;
            color: #64748b;
            font-weight: 500;
        }

        .foot {
            margin-top: 18px;
            border-top: 1px solid #e2e8f0;
            padding: 8px 0 0 14px;
            font-size: 8px;
            color: #94a3b8;
        }
    </style>
</head>
<body>
<div class="wrap">

    <table class="brand-table" cellspacing="0" cellpadding="0">
        <tr>
            <td class="brand-cell">
                <table width="100%" cellspacing="0" cellpadding="0">
                    <tr>
                        <td valign="middle">
                            <div class="co-name">PT. SELARASGRIYA SARANA UTAMA</div>
                            <div class="co-sub">Pasar Induk Surabaya Sidotopo &nbsp;·&nbsp; QIEOS POS Management System</div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td class="gold-bar">&nbsp;</td>
        </tr>
    </table>

    <div class="title-block">
        <div class="report-kicker">Laporan Tenant</div>
        <div class="report-title"><?= pdfEscape($title) ?></div>
        <div class="report-period">Periode <?= pdfEscape($periodLabel) ?></div>
    </div>

    <table class="meta-table" cellspacing="0" cellpadding="0">
        <tr>
            <td>
                <div class="meta-label">Jenis Laporan</div>
                <div class="meta-value"><?= pdfEscape($kind) ?><?= $isSingle ? ' · Per Tenant' : ' · Semua Tenant' ?></div>
            </td>
            <td>
                <div class="meta-label"><?= $isSingle ? 'Nama Tenant' : 'Cakupan' ?></div>
                <div class="meta-value"><?= $isSingle ? pdfEscape($tenantName !== '' ? $tenantName : '-') : 'Seluruh tenant terdaftar' ?></div>
            </td>
            <td>
                <div class="meta-label">Dicetak</div>
                <div class="meta-value"><?= pdfEscape($printedAt) ?></div>
            </td>
        </tr>
    </table>

    <table class="kpi-table" cellspacing="0" cellpadding="0">
        <tr>
            <td class="kpi-1">
                <div class="kpi-label">Jumlah Transaksi</div>
                <div class="kpi-value"><?= number_format($count, 0, ',', '.') ?></div>
                <div class="kpi-hint">Pembayaran dalam periode ini</div>
            </td>
            <td class="kpi-2">
                <div class="kpi-label">Total Pembayaran</div>
                <div class="kpi-value">Rp <?= number_format($total, 0, ',', '.') ?></div>
                <div class="kpi-hint">Akumulasi nilai transaksi</div>
            </td>
            <td class="kpi-3">
                <div class="kpi-label">Status</div>
                <div class="kpi-value"><?= number_format($paid, 0, ',', '.') ?> Lunas</div>
                <div class="kpi-hint"><?= $count > 0 ? number_format(($paid / $count) * 100, 0) . '% dari seluruh data' : 'Tidak ada data' ?></div>
            </td>
        </tr>
    </table>

    <table class="data-table" cellspacing="0" cellpadding="0">
        <thead>
            <tr>
                <th width="8%">No</th>
                <?php if (!$isSingle): ?>
                    <th width="32%">Nama Tenant</th>
                <?php endif; ?>
                <th width="<?= $isSingle ? '32%' : '20%' ?>">Tanggal</th>
                <th width="<?= $isSingle ? '35%' : '25%' ?>">Jumlah</th>
                <th width="15%">Status</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($count === 0): ?>
            <tr>
                <td colspan="<?= $colspan ?>" class="empty-cell">
                    Tidak ada data pembayaran pada periode yang dipilih.
                </td>
            </tr>
        <?php else: ?>
            <?php foreach ($rows as $i => $row): ?>
                <?php
                    $rowStatus = isset($row['status']) ? $row['status'] : '';
                    $isPaid = ($rowStatus === 'paid');
                    $statusLabel = $isPaid ? 'Lunas' : ($rowStatus !== '' ? ucfirst((string) $rowStatus) : '-');
                ?>
                <tr class="<?= $i % 2 === 0 ? 'row-a' : 'row-b' ?>">
                    <td class="c"><?= $i + 1 ?></td>
                    <?php if (!$isSingle): ?>
                        <td class="l tenant-name"><?= pdfEscape($row['tenant_name']) ?></td>
                    <?php endif; ?>
                    <td class="c"><?= pdfDateId($row['payment_date']) ?></td>
                    <td class="r">Rp <?= number_format((float) $row['cost_payment'], 0, ',', '.') ?></td>
                    <td class="c">
                        <span class="badge <?= $isPaid ? 'badge-paid' : 'badge-other' ?>">
                            <?= pdfEscape($statusLabel) ?>
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>
            <tr class="total-row">
                <td colspan="<?= $colspan - 2 ?>" class="r">TOTAL PEMBAYARAN</td>
                <td class="r total-amount">Rp <?= number_format($total, 0, ',', '.') ?></td>
                <td></td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>

    <table class="sign-table" cellspacing="0" cellpadding="0">
        <tr>
            <td class="sign-box">
                <div class="sign-title">Dicetak Oleh</div>
                <div><?= pdfEscape($printedAt) ?></div>
                <div class="sign-line"><?= pdfEscape($printedBy) ?></div>
                <div class="sign-role"><?= pdfEscape($printedRole) ?></div>
            </td>
            <td width="8%"></td>
            <td class="sign-box">
                <div class="sign-title">Mengetahui</div>
                <div>Surabaya, <?= pdfDateId(date('Y-m-d')) ?></div>
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

$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('isHtml5ParserEnabled', true);
$options->set('defaultFont', 'DejaVu Sans');
$options->setChroot(realpath(__DIR__ . '/../../..'));

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

$filename = $title2 . ' - ' . ($first_date !== '' ? date('d M Y', strtotime($first_date)) : 'Semua') . ' s.d. ' . ($last_date !== '' ? date('d M Y', strtotime($last_date)) : 'Semua') . '.pdf';

$dompdf->stream($filename, [
    'Attachment' => false
]);
