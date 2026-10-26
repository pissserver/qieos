<?php

include '../../../sessions/session.php';
include 'report-helper.php';

list($first, $last) = report_dates($conn);

$location = isset($_GET['location']) ? $_GET['location'] : 'gudang';

$startTs = $first . ' 00:00:00';
$endTs   = $last . ' 23:59:59';

function mutasi_scalar($conn, $sql){
    $r = mysqli_query($conn, $sql);
    if(!$r) return 0;
    $row = mysqli_fetch_row($r);
    return $row ? (int)$row[0] : 0;
}

/* Riwayat catatan adjustment terpisah: penambahan & pengurangan.
   $tag diisi saat mode gabungan supaya lokasi adjustment terlihat. */
function adj_note_list($conn, $id, $location, $startTs, $endTs, $tag = ''){
    $in  = [];
    $out = [];

    if($location === 'kantin'){
        $q = mysqli_query($conn, "SELECT qty, note FROM sales_stock WHERE product_id=$id AND type='adjustment' AND created_at BETWEEN '$startTs' AND '$endTs' ORDER BY created_at DESC");
        while($q && $r = mysqli_fetch_assoc($q)){
            $qv = (int)$r['qty'];
            if($qv > 0) $in[]  = '+' . $qv . ($tag !== '' ? ' (' . $tag . ')' : '') . ($r['note'] ? ' — ' . $r['note'] : '');
            else        $out[] = '−' . abs($qv) . ($tag !== '' ? ' (' . $tag . ')' : '') . ($r['note'] ? ' — ' . $r['note'] : '');
        }
    } else {
        $q = mysqli_query($conn, "SELECT type, qty, note FROM stock_adjustments WHERE product_id=$id AND location='gudang' AND created_at BETWEEN '$startTs' AND '$endTs' ORDER BY created_at DESC");
        while($q && $r = mysqli_fetch_assoc($q)){
            $line = ($r['type']==='in'?'+':'−') . (int)$r['qty'] . ($tag !== '' ? ' (' . $tag . ')' : '') . ($r['note'] ? ' — ' . $r['note'] : '');
            if($r['type']==='in') $in[] = $line;
            else                  $out[] = $line;
        }
    }

    return [$in, $out];
}

$products = [];
$q = mysqli_query($conn, "SELECT id, name, code, unit FROM products WHERE category != 'Additional' ORDER BY name ASC");
while($q && $p = mysqli_fetch_assoc($q)) $products[] = $p;

if(count($products) === 0){
    report_empty(8, 'Tidak ada produk terdaftar.');
    exit;
}

$no = 1;

foreach($products as $prod){
    $id = (int)$prod['id'];
    $unit = strtoupper($prod['unit'] ?: '');
    $unitHtml = $unit ? ' <small style="color:#94a3b8;">' . $unit . '</small>' : '';

    if($location === 'gudang'){
        $awal   = mutasi_scalar($conn, "SELECT COALESCE(SUM(pi.qty_stock),0) FROM purchase_items pi JOIN purchases p ON p.id=pi.purchase_id WHERE pi.product_id=$id AND pi.deleted_at IS NULL AND p.deleted_at IS NULL AND p.date<'$first'")
                + mutasi_scalar($conn, "SELECT COALESCE(SUM(qty),0) FROM stock_adjustments WHERE product_id=$id AND location='gudang' AND type='in' AND created_at<'$startTs'")
                - mutasi_scalar($conn, "SELECT COALESCE(SUM(qty),0) FROM sales_stock WHERE product_id=$id AND type='transfer' AND created_at<'$startTs'")
                - mutasi_scalar($conn, "SELECT COALESCE(SUM(qty),0) FROM stock_adjustments WHERE product_id=$id AND location='gudang' AND type='out' AND created_at<'$startTs'");

        $masuk  = mutasi_scalar($conn, "SELECT COALESCE(SUM(pi.qty_stock),0) FROM purchase_items pi JOIN purchases p ON p.id=pi.purchase_id WHERE pi.product_id=$id AND pi.deleted_at IS NULL AND p.deleted_at IS NULL AND p.date BETWEEN '$first' AND '$last'");

        $keluar = mutasi_scalar($conn, "SELECT COALESCE(SUM(qty),0) FROM sales_stock WHERE product_id=$id AND type='transfer' AND created_at BETWEEN '$startTs' AND '$endTs'");

        $adjIn  = mutasi_scalar($conn, "SELECT COALESCE(SUM(qty),0) FROM stock_adjustments WHERE product_id=$id AND location='gudang' AND type='in' AND created_at BETWEEN '$startTs' AND '$endTs'");
        $adjOut = mutasi_scalar($conn, "SELECT COALESCE(SUM(qty),0) FROM stock_adjustments WHERE product_id=$id AND location='gudang' AND type='out' AND created_at BETWEEN '$startTs' AND '$endTs'");

        list($adjInList, $adjOutList) = adj_note_list($conn, $id, 'gudang', $startTs, $endTs);

    } elseif($location === 'kantin'){
        $awal   = mutasi_scalar($conn, "SELECT COALESCE(SUM(qty),0) FROM sales_stock WHERE product_id=$id AND created_at<'$startTs'");
        $masuk  = mutasi_scalar($conn, "SELECT COALESCE(SUM(qty),0) FROM sales_stock WHERE product_id=$id AND type IN ('transfer','return') AND created_at BETWEEN '$startTs' AND '$endTs'");
        $keluar = mutasi_scalar($conn, "SELECT COALESCE(SUM(-qty),0) FROM sales_stock WHERE product_id=$id AND type='sale' AND created_at BETWEEN '$startTs' AND '$endTs'");

        $adjIn  = mutasi_scalar($conn, "SELECT COALESCE(SUM(qty),0) FROM sales_stock WHERE product_id=$id AND type='adjustment' AND qty>0 AND created_at BETWEEN '$startTs' AND '$endTs'");
        $adjOut = mutasi_scalar($conn, "SELECT COALESCE(SUM(-qty),0) FROM sales_stock WHERE product_id=$id AND type='adjustment' AND qty<0 AND created_at BETWEEN '$startTs' AND '$endTs'");

        list($adjInList, $adjOutList) = adj_note_list($conn, $id, 'kantin', $startTs, $endTs);

    } else {
        $gAwal   = mutasi_scalar($conn, "SELECT COALESCE(SUM(pi.qty_stock),0) FROM purchase_items pi JOIN purchases p ON p.id=pi.purchase_id WHERE pi.product_id=$id AND pi.deleted_at IS NULL AND p.deleted_at IS NULL AND p.date<'$first'")
                 + mutasi_scalar($conn, "SELECT COALESCE(SUM(qty),0) FROM stock_adjustments WHERE product_id=$id AND location='gudang' AND type='in' AND created_at<'$startTs'");
        $gKeluar = mutasi_scalar($conn, "SELECT COALESCE(SUM(qty),0) FROM sales_stock WHERE product_id=$id AND type='transfer' AND created_at<'$startTs'")
                 + mutasi_scalar($conn, "SELECT COALESCE(SUM(qty),0) FROM stock_adjustments WHERE product_id=$id AND location='gudang' AND type='out' AND created_at<'$startTs'");
        $kAwal   = mutasi_scalar($conn, "SELECT COALESCE(SUM(qty),0) FROM sales_stock WHERE product_id=$id AND created_at<'$startTs'");
        $awal    = ($gAwal - $gKeluar) + $kAwal;

        $masuk   = mutasi_scalar($conn, "SELECT COALESCE(SUM(pi.qty_stock),0) FROM purchase_items pi JOIN purchases p ON p.id=pi.purchase_id WHERE pi.product_id=$id AND pi.deleted_at IS NULL AND p.deleted_at IS NULL AND p.date BETWEEN '$first' AND '$last'");
        $keluar  = mutasi_scalar($conn, "SELECT COALESCE(SUM(-qty),0) FROM sales_stock WHERE product_id=$id AND type='sale' AND created_at BETWEEN '$startTs' AND '$endTs'");

        $adjIn   = mutasi_scalar($conn, "SELECT COALESCE(SUM(qty),0) FROM stock_adjustments WHERE product_id=$id AND location='gudang' AND type='in' AND created_at BETWEEN '$startTs' AND '$endTs'")
                 + mutasi_scalar($conn, "SELECT COALESCE(SUM(qty),0) FROM sales_stock WHERE product_id=$id AND type='adjustment' AND qty>0 AND created_at BETWEEN '$startTs' AND '$endTs'");
        $adjOut  = mutasi_scalar($conn, "SELECT COALESCE(SUM(qty),0) FROM stock_adjustments WHERE product_id=$id AND location='gudang' AND type='out' AND created_at BETWEEN '$startTs' AND '$endTs'")
                 + mutasi_scalar($conn, "SELECT COALESCE(SUM(-qty),0) FROM sales_stock WHERE product_id=$id AND type='adjustment' AND qty<0 AND created_at BETWEEN '$startTs' AND '$endTs'");

        list($gIn, $gOut) = adj_note_list($conn, $id, 'gudang', $startTs, $endTs, 'gudang');
        list($kIn, $kOut) = adj_note_list($conn, $id, 'kantin', $startTs, $endTs, 'kantin');
        $adjInList  = array_merge($gIn, $kIn);
        $adjOutList = array_merge($gOut, $kOut);
    }

    /* Adjustment tidak masuk qty masuk/keluar, hanya dihitung
       menuju saldo akhir. */
    $akhir = max(0, $awal + $masuk - $keluar + $adjIn - $adjOut);

    $adjInCell  = '<span class="stk-adj-none">–</span>';
    if(count($adjInList) > 0){
        $adjInCell = '<div class="stk-adj-line stk-adj-in">';
        foreach($adjInList as $line) $adjInCell .= '<div><b>' . htmlspecialchars(explode(' — ', $line)[0]) . '</b>' . (strpos($line, ' — ') !== false ? ' <span>' . htmlspecialchars(explode(' — ', $line, 2)[1]) . '</span>' : '') . '</div>';
        $adjInCell .= '</div>';
    }

    $adjOutCell = '<span class="stk-adj-none">–</span>';
    if(count($adjOutList) > 0){
        $adjOutCell = '<div class="stk-adj-line stk-adj-out">';
        foreach($adjOutList as $line) $adjOutCell .= '<div><b>' . htmlspecialchars(explode(' — ', $line)[0]) . '</b>' . (strpos($line, ' — ') !== false ? ' <span>' . htmlspecialchars(explode(' — ', $line, 2)[1]) . '</span>' : '') . '</div>';
        $adjOutCell .= '</div>';
    }

    echo '<tr>'
        . '<td class="text-center">' . $no++ . '</td>'
        . '<td><div style="font-weight:700;">' . htmlspecialchars($prod['name']) . '</div><div style="font-size:12px; color:#64748b;">' . htmlspecialchars($prod['code']) . '</div></td>'
        . '<td class="text-center">' . number_format($awal) . $unitHtml . '</td>'
        . '<td class="text-center" style="color:#047857;">+' . number_format($masuk) . $unitHtml . '</td>'
        . '<td class="text-center" style="color:#dc2626;">−' . number_format($keluar) . $unitHtml . '</td>'
        . '<td class="text-center">' . $adjInCell . '</td>'
        . '<td class="text-center">' . $adjOutCell . '</td>'
        . '<td class="text-center fw-bold">' . number_format($akhir) . $unitHtml . '</td>'
        . '</tr>';
}
