<?php
include '../../sessions/session.php';

/* ============================================================
   Endpoint panel "Mutasi Stok" (pages/stock/stock.php).

   Mengembalikan HTML dua pane (Gudang & Kantin) untuk satu produk,
   langsung dirender ke panel kanan lewat fetch. Tiap pane berisi
   4 kartu ringkasan (qty awal / masuk / keluar / akhir) + tabel
   riwayat pergerakan.

   Sumber data:
   - Gudang : purchase_items.qty (masuk) & sales_stock type 'transfer'
              (keluar ke kantin).
   - Kantin : ledger sales_stock (balance/transfer/sale/return).
   ============================================================ */

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

/* Validasi ketat format tanggal supaya nilai yang diinterpolasi ke
   query selalu berbentuk YYYY-MM-DD yang aman. */
function mutasi_date($value, $fallback){
    $value = is_string($value) ? trim($value) : '';
    if($value === '') return $fallback;
    $d = DateTime::createFromFormat('Y-m-d', $value);
    if(!$d || $d->format('Y-m-d') !== $value) return $fallback;
    return $value;
}

$from = mutasi_date(isset($_GET['from']) ? $_GET['from'] : '', date('Y-m-01'));
$to   = mutasi_date(isset($_GET['to'])   ? $_GET['to']   : '', date('Y-m-d'));
if($from > $to){ $swap = $from; $from = $to; $to = $swap; }

$startTs = $from . ' 00:00:00';
$endTs   = $to   . ' 23:59:59';

$prod = null;
$pq = mysqli_query($conn, "SELECT id,name,code,unit,photo FROM products WHERE id = $id");
if($pq) $prod = mysqli_fetch_assoc($pq);

if(!$prod){
    echo '<div class="mutasi-pane-empty"><i class="fas fa-box-open"></i><div>Produk tidak ditemukan</div></div>';
    exit;
}

$unit = !empty($prod['unit']) ? strtoupper($prod['unit']) : '';

$bulan = [
    1 => 'Januari','Februari','Maret','April','Mei','Juni',
    'Juli','Agustus','September','Oktober','November','Desember'
];

function mutasi_tgl($ymd, $bulan){
    $ts = strtotime($ymd);
    if(!$ts) return '-';
    return date('d', $ts) . ' ' . $bulan[(int)date('n', $ts)] . ' ' . date('Y', $ts);
}

function mutasi_scalar($conn, $sql){
    $r = mysqli_query($conn, $sql);
    if(!$r) return 0;
    $row = mysqli_fetch_row($r);
    return $row ? (int)$row[0] : 0;
}

function mutasi_stat($label, $value, $unit, $icon, $tone){
    $unitHtml = $unit !== '' ? '<em>' . htmlspecialchars($unit) . '</em>' : '';
    return '<div class="mutasi-stat mutasi-stat-' . $tone . '">'
        . '<div class="mutasi-stat-top">'
        .     '<span class="mutasi-stat-ico"><i class="fas ' . $icon . '"></i></span>'
        .     '<span class="mutasi-stat-label">' . $label . '</span>'
        . '</div>'
        . '<div class="mutasi-stat-value">' . number_format((int)$value) . $unitHtml . '</div>'
        . '</div>';
}

function mutasi_rows_html($rows, $bulan){
    $html = '';
    foreach($rows as $r){
        $masuk  = $r['masuk']  > 0 ? '<span class="mutasi-in">+'  . number_format((int)$r['masuk'])  . '</span>' : '<span class="mutasi-nil">–</span>';
        $keluar = $r['keluar'] > 0 ? '<span class="mutasi-out">−' . number_format((int)$r['keluar']) . '</span>' : '<span class="mutasi-nil">–</span>';

        $sub = $r['sub'] !== '' ? '<span class="mutasi-ket-sub">' . htmlspecialchars($r['sub']) . '</span>' : '';

        $html .= '<tr>'
            . '<td data-label="Tanggal"><div class="date-main"><i class="far fa-calendar"></i> ' . mutasi_tgl($r['tgl'], $bulan) . '</div></td>'
            . '<td data-label="Keterangan"><div class="mutasi-ket"><span class="mutasi-ket-title">' . htmlspecialchars($r['title']) . '</span>' . $sub . '</div></td>'
            . '<td data-label="Masuk" class="text-center">' . $masuk . '</td>'
            . '<td data-label="Keluar" class="text-center">' . $keluar . '</td>'
            . '<td data-label="Saldo" class="text-center"><span class="mutasi-saldo">' . number_format((int)$r['saldo']) . '</span></td>'
            . '</tr>';
    }
    return $html;
}

/* ==================== GUDANG ==================== */
$gMasukBefore  = mutasi_scalar($conn, "SELECT COALESCE(SUM(pi.qty),0) FROM purchase_items pi JOIN purchases p ON p.id = pi.purchase_id WHERE pi.product_id = $id AND pi.deleted_at IS NULL AND p.deleted_at IS NULL AND p.date < '$from'");
$gKeluarBefore = mutasi_scalar($conn, "SELECT COALESCE(SUM(qty),0) FROM sales_stock WHERE product_id = $id AND type = 'transfer' AND created_at < '$startTs'");
$gMasuk        = mutasi_scalar($conn, "SELECT COALESCE(SUM(pi.qty),0) FROM purchase_items pi JOIN purchases p ON p.id = pi.purchase_id WHERE pi.product_id = $id AND pi.deleted_at IS NULL AND p.deleted_at IS NULL AND p.date BETWEEN '$from' AND '$to'");
$gKeluar       = mutasi_scalar($conn, "SELECT COALESCE(SUM(qty),0) FROM sales_stock WHERE product_id = $id AND type = 'transfer' AND created_at BETWEEN '$startTs' AND '$endTs'");

$gAwal  = max(0, $gMasukBefore - $gKeluarBefore);
$gAkhir = max(0, $gAwal + $gMasuk - $gKeluar);

$rowsG = [];

$qp = mysqli_query($conn, "
    SELECT p.date AS tgl, p.form AS form, COALESCE(SUM(pi.qty),0) AS qty
    FROM purchase_items pi
    JOIN purchases p ON p.id = pi.purchase_id
    WHERE pi.product_id = $id
      AND pi.deleted_at IS NULL
      AND p.deleted_at IS NULL
      AND p.date BETWEEN '$from' AND '$to'
    GROUP BY p.id
    ORDER BY p.date ASC, p.id ASC
");
while($qp && $r = mysqli_fetch_assoc($qp)){
    $rowsG[] = [
        'tgl'    => $r['tgl'],
        'title'  => 'Pembelian Masuk',
        'sub'    => $r['form'] ? $r['form'] : '',
        'masuk'  => (int)$r['qty'],
        'keluar' => 0,
        'saldo'  => 0
    ];
}

$qt = mysqli_query($conn, "
    SELECT DATE(created_at) AS tgl, COALESCE(SUM(qty),0) AS qty
    FROM sales_stock
    WHERE product_id = $id
      AND type = 'transfer'
      AND created_at BETWEEN '$startTs' AND '$endTs'
    GROUP BY DATE(created_at)
    ORDER BY tgl ASC
");
while($qt && $r = mysqli_fetch_assoc($qt)){
    $rowsG[] = [
        'tgl'    => $r['tgl'],
        'title'  => 'Transfer ke Kantin',
        'sub'    => '',
        'masuk'  => 0,
        'keluar' => (int)$r['qty'],
        'saldo'  => 0
    ];
}

usort($rowsG, function($a, $b){
    if($a['tgl'] === $b['tgl']) return 0;
    return ($a['tgl'] < $b['tgl']) ? -1 : 1;
});

$saldoG = $gAwal;
foreach($rowsG as $i => $r){
    $saldoG += $r['masuk'] - $r['keluar'];
    $rowsG[$i]['saldo'] = $saldoG;
}

/* ==================== KANTIN ==================== */
$kAwal   = mutasi_scalar($conn, "SELECT COALESCE(SUM(qty),0) FROM sales_stock WHERE product_id = $id AND created_at < '$startTs'");
$kMasuk  = mutasi_scalar($conn, "SELECT COALESCE(SUM(qty),0) FROM sales_stock WHERE product_id = $id AND type IN ('transfer','return') AND created_at BETWEEN '$startTs' AND '$endTs'");
$kKeluar = mutasi_scalar($conn, "SELECT COALESCE(SUM(-qty),0) FROM sales_stock WHERE product_id = $id AND type = 'sale' AND created_at BETWEEN '$startTs' AND '$endTs'");

$kAkhir = max(0, $kAwal + $kMasuk - $kKeluar);

$rowsK = [];
$labelsK = [
    'balance'  => 'Saldo Awal',
    'transfer' => 'Transfer dari Gudang',
    'sale'     => 'Penjualan Kantin',
    'return'   => 'Retur Pesanan'
];

$qk = mysqli_query($conn, "
    SELECT created_at, qty, type
    FROM sales_stock
    WHERE product_id = $id
      AND created_at BETWEEN '$startTs' AND '$endTs'
    ORDER BY created_at ASC, id ASC
");
$saldoK = $kAwal;
while($qk && $r = mysqli_fetch_assoc($qk)){
    $q = (int)$r['qty'];
    $saldoK += $q;
    $rowsK[] = [
        'tgl'    => substr($r['created_at'], 0, 10),
        'title'  => isset($labelsK[$r['type']]) ? $labelsK[$r['type']] : ucfirst($r['type']),
        'sub'    => '',
        'masuk'  => $q > 0 ? $q : 0,
        'keluar' => $q < 0 ? -$q : 0,
        'saldo'  => $saldoK
    ];
}
?>
<div class="mutasi-product">
    <div class="mutasi-product-info">
        <div class="mutasi-product-name"><?= htmlspecialchars($prod['name']) ?></div>
        <div class="mutasi-product-code"><?= htmlspecialchars($prod['code']) ?></div>
    </div>
    <div class="mutasi-product-range">
        <i class="far fa-calendar"></i>
        <?= mutasi_tgl($from, $bulan) ?> &ndash; <?= mutasi_tgl($to, $bulan) ?>
    </div>
</div>

<section class="mutasi-pane is-active" data-pane="gudang">
    <div class="mutasi-stats">
        <?= mutasi_stat('Qty Awal',  $gAwal,  $unit, 'fa-box-archive',      'awal') ?>
        <?= mutasi_stat('Qty Masuk', $gMasuk, $unit, 'fa-arrow-down-long',  'masuk') ?>
        <?= mutasi_stat('Qty Keluar',$gKeluar,$unit, 'fa-arrow-up-long',    'keluar') ?>
        <?= mutasi_stat('Qty Akhir', $gAkhir, $unit, 'fa-cubes-stacked',    'akhir') ?>
    </div>

    <div class="mutasi-riwayat">
        <div class="mutasi-riwayat-head">
            <span class="mutasi-riwayat-title"><i class="fas fa-clock-rotate-left"></i> Riwayat Mutasi Gudang</span>
        </div>
        <div class="table-responsive-wrap">
            <table class="table-mutasi" id="tableMutasiGudang">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Keterangan</th>
                        <th class="text-center">Masuk</th>
                        <th class="text-center">Keluar</th>
                        <th class="text-center">Saldo</th>
                    </tr>
                </thead>
                <tbody>
                    <?= mutasi_rows_html($rowsG, $bulan) ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<section class="mutasi-pane" data-pane="kantin">
    <div class="mutasi-stats">
        <?= mutasi_stat('Qty Awal',  $kAwal,  $unit, 'fa-box-archive',      'awal') ?>
        <?= mutasi_stat('Qty Masuk', $kMasuk, $unit, 'fa-arrow-down-long',  'masuk') ?>
        <?= mutasi_stat('Qty Keluar',$kKeluar,$unit, 'fa-arrow-up-long',    'keluar') ?>
        <?= mutasi_stat('Qty Akhir', $kAkhir, $unit, 'fa-cubes-stacked',    'akhir') ?>
    </div>

    <div class="mutasi-riwayat">
        <div class="mutasi-riwayat-head">
            <span class="mutasi-riwayat-title"><i class="fas fa-clock-rotate-left"></i> Riwayat Mutasi Kantin</span>
        </div>
        <div class="table-responsive-wrap">
            <table class="table-mutasi" id="tableMutasiKantin">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Keterangan</th>
                        <th class="text-center">Masuk</th>
                        <th class="text-center">Keluar</th>
                        <th class="text-center">Saldo</th>
                    </tr>
                </thead>
                <tbody>
                    <?= mutasi_rows_html($rowsK, $bulan) ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
