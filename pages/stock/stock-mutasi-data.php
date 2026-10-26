<?php

/* ============================================================
   Lapisan data panel "Mutasi Stok".

   Dipakai bersama oleh:
   - stock-mutasi.php        -> render HTML dua pane.
   - stock-mutasi-export.php -> ekspor Excel sesuai tab aktif.

   Menghasilkan variabel:
   - $id, $from, $to, $startTs, $endTs
   - $prod (null kalau produk tidak ada), $unit, $bulan
   - Gudang : $gAwal, $gMasuk, $gKeluar, $gAkhir, $rowsG
   - Kantin : $kAwal, $kMasuk, $kKeluar, $kAkhir, $rowsK

   Sumber data:
   - Gudang : purchase_items.qty_stock (masuk) & sales_stock type 'transfer'
              (keluar ke kantin).
   - Kantin : ledger sales_stock (balance/transfer/sale/return).
   ============================================================ */

if(!function_exists('mutasi_date')){
    /* Validasi ketat format tanggal supaya nilai yang diinterpolasi ke
       query selalu berbentuk YYYY-MM-DD yang aman. */
    function mutasi_date($value, $fallback){
        $value = is_string($value) ? trim($value) : '';
        if($value === '') return $fallback;
        $d = DateTime::createFromFormat('Y-m-d', $value);
        if(!$d || $d->format('Y-m-d') !== $value) return $fallback;
        return $value;
    }
}

if(!function_exists('mutasi_tgl')){
    function mutasi_tgl($ymd, $bulan){
        $ts = strtotime($ymd);
        if(!$ts) return '-';
        return date('d', $ts) . ' ' . $bulan[(int)date('n', $ts)] . ' ' . date('Y', $ts);
    }
}

if(!function_exists('mutasi_scalar')){
    function mutasi_scalar($conn, $sql){
        $r = mysqli_query($conn, $sql);
        if(!$r) return 0;
        $row = mysqli_fetch_row($r);
        return $row ? (int)$row[0] : 0;
    }
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$from = mutasi_date(isset($_GET['from']) ? $_GET['from'] : '', date('Y-m-01'));
$to   = mutasi_date(isset($_GET['to'])   ? $_GET['to']   : '', date('Y-m-d'));
if($from > $to){ $swap = $from; $from = $to; $to = $swap; }

$startTs = $from . ' 00:00:00';
$endTs   = $to   . ' 23:59:59';

$prod = null;
$pq = mysqli_query($conn, "SELECT id,name,code,unit,photo FROM products WHERE id = $id");
if($pq) $prod = mysqli_fetch_assoc($pq);

if(!$prod){
    return;
}

$unit = !empty($prod['unit']) ? strtoupper($prod['unit']) : '';

$bulan = [
    1 => 'Januari','Februari','Maret','April','Mei','Juni',
    'Juli','Agustus','September','Oktober','November','Desember'
];

/* ==================== GUDANG ==================== */
$gMasukBefore  = mutasi_scalar($conn, "SELECT COALESCE(SUM(pi.qty_stock),0) FROM purchase_items pi JOIN purchases p ON p.id = pi.purchase_id WHERE pi.product_id = $id AND pi.deleted_at IS NULL AND p.deleted_at IS NULL AND p.date < '$from'")
               + mutasi_scalar($conn, "SELECT COALESCE(SUM(qty),0) FROM stock_adjustments WHERE product_id = $id AND location = 'gudang' AND type = 'in' AND created_at < '$startTs'");
$gKeluarBefore = mutasi_scalar($conn, "SELECT COALESCE(SUM(qty),0) FROM sales_stock WHERE product_id = $id AND type = 'transfer' AND created_at < '$startTs'")
               + mutasi_scalar($conn, "SELECT COALESCE(SUM(qty),0) FROM stock_adjustments WHERE product_id = $id AND location = 'gudang' AND type = 'out' AND created_at < '$startTs'");
$gMasuk        = mutasi_scalar($conn, "SELECT COALESCE(SUM(pi.qty_stock),0) FROM purchase_items pi JOIN purchases p ON p.id = pi.purchase_id WHERE pi.product_id = $id AND pi.deleted_at IS NULL AND p.deleted_at IS NULL AND p.date BETWEEN '$from' AND '$to'")
               + mutasi_scalar($conn, "SELECT COALESCE(SUM(qty),0) FROM stock_adjustments WHERE product_id = $id AND location = 'gudang' AND type = 'in' AND created_at BETWEEN '$startTs' AND '$endTs'");
$gKeluar       = mutasi_scalar($conn, "SELECT COALESCE(SUM(qty),0) FROM sales_stock WHERE product_id = $id AND type = 'transfer' AND created_at BETWEEN '$startTs' AND '$endTs'")
               + mutasi_scalar($conn, "SELECT COALESCE(SUM(qty),0) FROM stock_adjustments WHERE product_id = $id AND location = 'gudang' AND type = 'out' AND created_at BETWEEN '$startTs' AND '$endTs'");

$gAwal  = max(0, $gMasukBefore - $gKeluarBefore);
$gAkhir = max(0, $gAwal + $gMasuk - $gKeluar);

$rowsG = [];
$gIdx = 0;

$qp = mysqli_query($conn, "
    SELECT p.date AS tgl, p.created_at AS ts, p.form AS form, COALESCE(SUM(pi.qty_stock),0) AS qty
    FROM purchase_items pi
    JOIN purchases p ON p.id = pi.purchase_id
    WHERE pi.product_id = $id
      AND pi.deleted_at IS NULL
      AND p.deleted_at IS NULL
      AND p.date BETWEEN '$from' AND '$to'
    GROUP BY p.id
    ORDER BY p.created_at ASC, p.id ASC
");
while($qp && $r = mysqli_fetch_assoc($qp)){
    $rowsG[] = [
        'tgl'    => $r['tgl'],
        'ts'     => $r['ts'],
        'title'  => 'Pembelian Masuk',
        'sub'    => $r['form'] ? $r['form'] : '',
        'masuk'  => (int)$r['qty'],
        'keluar' => 0,
        'saldo'  => 0,
        'idx'    => $gIdx++
    ];
}

$qt = mysqli_query($conn, "
    SELECT created_at AS ts, DATE(created_at) AS tgl, qty
    FROM sales_stock
    WHERE product_id = $id
      AND type = 'transfer'
      AND created_at BETWEEN '$startTs' AND '$endTs'
    ORDER BY created_at ASC, id ASC
");
while($qt && $r = mysqli_fetch_assoc($qt)){
    $rowsG[] = [
        'tgl'    => $r['tgl'],
        'ts'     => $r['ts'],
        'title'  => 'Transfer ke Kantin',
        'sub'    => '',
        'masuk'  => 0,
        'keluar' => (int)$r['qty'],
        'saldo'  => 0,
        'idx'    => $gIdx++
    ];
}

$qa = mysqli_query($conn, "
    SELECT sa.created_at AS ts, DATE(sa.created_at) AS tgl, sa.type, sa.qty, sa.note, p.form
    FROM stock_adjustments sa
    LEFT JOIN purchase_items pi ON pi.id = sa.purchase_item_id
    LEFT JOIN purchases p ON p.id = pi.purchase_id
    WHERE sa.product_id = $id
      AND sa.location = 'gudang'
      AND sa.created_at BETWEEN '$startTs' AND '$endTs'
    ORDER BY sa.created_at ASC, sa.id ASC
");
while($qa && $r = mysqli_fetch_assoc($qa)){
    $isIn = $r['type'] === 'in';
    $rowsG[] = [
        'tgl'    => $r['tgl'],
        'ts'     => $r['ts'],
        'title'  => $isIn ? 'Adjustment Penambahan' : 'Adjustment Pengurangan',
        'sub'    => ($r['note'] ?: '') . ($r['form'] ? ' • ' . $r['form'] : ''),
        'masuk'  => $isIn ? (int)$r['qty'] : 0,
        'keluar' => $isIn ? 0 : (int)$r['qty'],
        'saldo'  => 0,
        'idx'    => $gIdx++
    ];
}

/* Urut kronologis memakai waktu kejadian ASLI (created_at), bukan
   menebak "pembelian dulu baru transfer". 'idx' jadi tie-breaker
   karena usort PHP < 8 TIDAK stabil. Dengan begitu array_reverse di
   bawah menghasilkan: kejadian terbaru di atas + saldo berjalan yang
   benar sesuai urutan waktu (mis. Pembelian Masuk setelah Transfer
   = saldo transfer + qty, bukan saldo lama + qty). */
usort($rowsG, function($a, $b){
    if($a['ts'] !== $b['ts']) return ($a['ts'] < $b['ts']) ? -1 : 1;
    if($a['idx'] === $b['idx']) return 0;
    return ($a['idx'] < $b['idx']) ? -1 : 1;
});

$saldoG = $gAwal;
foreach($rowsG as $i => $r){
    $saldoG += $r['masuk'] - $r['keluar'];
    $rowsG[$i]['saldo'] = $saldoG;
}

/* Tampilkan terbaru di atas: saldo tetap saldo saat event terjadi,
   hanya urutan baris yang dibalik. */
$rowsG = array_reverse($rowsG);

/* ==================== KANTIN ==================== */
$kAwal   = mutasi_scalar($conn, "SELECT COALESCE(SUM(qty),0) FROM sales_stock WHERE product_id = $id AND created_at < '$startTs'");
$kMasuk  = mutasi_scalar($conn, "SELECT COALESCE(SUM(qty),0) FROM sales_stock WHERE product_id = $id AND type IN ('transfer','return') AND created_at BETWEEN '$startTs' AND '$endTs'")
         + mutasi_scalar($conn, "SELECT COALESCE(SUM(qty),0) FROM sales_stock WHERE product_id = $id AND type = 'adjustment' AND qty > 0 AND created_at BETWEEN '$startTs' AND '$endTs'");
$kKeluar = mutasi_scalar($conn, "SELECT COALESCE(SUM(-qty),0) FROM sales_stock WHERE product_id = $id AND type = 'sale' AND created_at BETWEEN '$startTs' AND '$endTs'")
         + mutasi_scalar($conn, "SELECT COALESCE(SUM(-qty),0) FROM sales_stock WHERE product_id = $id AND type = 'adjustment' AND qty < 0 AND created_at BETWEEN '$startTs' AND '$endTs'");

$kAkhir = max(0, $kAwal + $kMasuk - $kKeluar);

$rowsK = [];
$labelsK = [
    'balance'  => 'Saldo Awal',
    'transfer' => 'Transfer dari Gudang',
    'sale'     => 'Penjualan Kantin',
    'return'   => 'Retur Pesanan'
];

$qk = mysqli_query($conn, "
    SELECT created_at, qty, type, note
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
        'title'  => $r['type'] === 'adjustment' ? ($q > 0 ? 'Adjustment Penambahan' : 'Adjustment Pengurangan') : (isset($labelsK[$r['type']]) ? $labelsK[$r['type']] : ucfirst($r['type'])),
        'sub'    => $r['note'] ?: '',
        'masuk'  => $q > 0 ? $q : 0,
        'keluar' => $q < 0 ? -$q : 0,
        'saldo'  => $saldoK
    ];
}

/* Tampilkan terbaru di atas (saldo tetap per-event saat dibangun). */
$rowsK = array_reverse($rowsK);
