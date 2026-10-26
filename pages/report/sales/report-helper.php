<?php

function report_esc($conn, $value)
{
    return mysqli_real_escape_string($conn, (string) $value);
}

function report_dates($conn)
{
    $first = isset($_GET['first_date']) ? $_GET['first_date'] : '';
    $last  = isset($_GET['last_date']) ? $_GET['last_date'] : '';

    return array(
        report_esc($conn, $first),
        report_esc($conn, $last)
    );
}

function report_rp($amount)
{
    return 'Rp ' . number_format((float) $amount, 0, ',', '.');
}

function report_date_id($date)
{
    return date('d M Y', strtotime($date));
}

function report_status_badge($status)
{
    $status = strtolower((string) $status);

    if ($status === 'paid') {
        return '<span class="status-paid">Terbayar</span>';
    }

    if ($status === 'waiting') {
        return '<span class="status-partial">Waiting</span>';
    }

    if ($status === 'cancelled') {
        return '<span class="status-unpaid">Dibatalkan</span>';
    }

    return '<span class="status-partial">' . htmlspecialchars($status) . '</span>';
}

function report_empty($colspan, $message)
{
    echo '<tr>';
    echo '<td colspan="' . (int) $colspan . '" class="text-center py-4 text-muted">';
    echo '<i class="fas fa-inbox mb-2" style="font-size:24px;"></i>';
    echo '<div>' . htmlspecialchars($message) . '</div>';
    echo '</td>';
    echo '</tr>';
}

function report_foot($amount)
{
    echo '<!--SPLIT_FOOT-->';
    echo report_rp($amount);
}

function report_scalar($conn, $sql)
{
    $query = mysqli_query($conn, $sql);
    if (!$query) {
        return 0;
    }

    $row = mysqli_fetch_assoc($query);
    if (!$row) {
        return 0;
    }

    $value = reset($row);
    return (float) $value;
}

/* ============================================================
   Mutasi stok per produk untuk laporan stok (tab 'stock').
   Dipakai bersama oleh export-excel.php dan print-pdf.php.
   ============================================================ */
if (!function_exists('stock_mutation_row')) {
    function stock_mutation_row($conn, $id, $location, $first, $last, $startTs, $endTs)
    {
        $S = function ($sql) use ($conn) {
            $r = mysqli_query($conn, $sql);
            $row = $r ? mysqli_fetch_row($r) : null;
            return $row ? (int) $row[0] : 0;
        };

        if ($location === 'gudang') {
            $awal   = $S("SELECT COALESCE(SUM(pi.qty_stock),0) FROM purchase_items pi JOIN purchases p ON p.id=pi.purchase_id WHERE pi.product_id=$id AND pi.deleted_at IS NULL AND p.deleted_at IS NULL AND p.date<'$first'")
                    + $S("SELECT COALESCE(SUM(qty),0) FROM stock_adjustments WHERE product_id=$id AND location='gudang' AND type='in' AND created_at<'$startTs'")
                    - $S("SELECT COALESCE(SUM(qty),0) FROM sales_stock WHERE product_id=$id AND type='transfer' AND created_at<'$startTs'")
                    - $S("SELECT COALESCE(SUM(qty),0) FROM stock_adjustments WHERE product_id=$id AND location='gudang' AND type='out' AND created_at<'$startTs'");
            $masuk  = $S("SELECT COALESCE(SUM(pi.qty_stock),0) FROM purchase_items pi JOIN purchases p ON p.id=pi.purchase_id WHERE pi.product_id=$id AND pi.deleted_at IS NULL AND p.deleted_at IS NULL AND p.date BETWEEN '$first' AND '$last'");
            $keluar = $S("SELECT COALESCE(SUM(qty),0) FROM sales_stock WHERE product_id=$id AND type='transfer' AND created_at BETWEEN '$startTs' AND '$endTs'");
            $adjIn  = $S("SELECT COALESCE(SUM(qty),0) FROM stock_adjustments WHERE product_id=$id AND location='gudang' AND type='in' AND created_at BETWEEN '$startTs' AND '$endTs'");
            $adjOut = $S("SELECT COALESCE(SUM(qty),0) FROM stock_adjustments WHERE product_id=$id AND location='gudang' AND type='out' AND created_at BETWEEN '$startTs' AND '$endTs'");
            list($inNotes, $outNotes) = stock_adj_notes($conn, $id, 'gudang', $startTs, $endTs);
        } elseif ($location === 'kantin') {
            $awal   = $S("SELECT COALESCE(SUM(qty),0) FROM sales_stock WHERE product_id=$id AND created_at<'$startTs'");
            $masuk  = $S("SELECT COALESCE(SUM(qty),0) FROM sales_stock WHERE product_id=$id AND type IN ('transfer','return') AND created_at BETWEEN '$startTs' AND '$endTs'");
            $keluar = $S("SELECT COALESCE(SUM(-qty),0) FROM sales_stock WHERE product_id=$id AND type='sale' AND created_at BETWEEN '$startTs' AND '$endTs'");
            $adjIn  = $S("SELECT COALESCE(SUM(qty),0) FROM sales_stock WHERE product_id=$id AND type='adjustment' AND qty>0 AND created_at BETWEEN '$startTs' AND '$endTs'");
            $adjOut = $S("SELECT COALESCE(SUM(-qty),0) FROM sales_stock WHERE product_id=$id AND type='adjustment' AND qty<0 AND created_at BETWEEN '$startTs' AND '$endTs'");
            list($inNotes, $outNotes) = stock_adj_notes($conn, $id, 'kantin', $startTs, $endTs);
        } else {
            $gAwal = $S("SELECT COALESCE(SUM(pi.qty_stock),0) FROM purchase_items pi JOIN purchases p ON p.id=pi.purchase_id WHERE pi.product_id=$id AND pi.deleted_at IS NULL AND p.deleted_at IS NULL AND p.date<'$first'")
                   + $S("SELECT COALESCE(SUM(qty),0) FROM stock_adjustments WHERE product_id=$id AND location='gudang' AND type='in' AND created_at<'$startTs'");
            $gOut  = $S("SELECT COALESCE(SUM(qty),0) FROM sales_stock WHERE product_id=$id AND type='transfer' AND created_at<'$startTs'")
                   + $S("SELECT COALESCE(SUM(qty),0) FROM stock_adjustments WHERE product_id=$id AND location='gudang' AND type='out' AND created_at<'$startTs'");
            $kAwal = $S("SELECT COALESCE(SUM(qty),0) FROM sales_stock WHERE product_id=$id AND created_at<'$startTs'");
            $awal  = ($gAwal - $gOut) + $kAwal;

            $masuk  = $S("SELECT COALESCE(SUM(pi.qty_stock),0) FROM purchase_items pi JOIN purchases p ON p.id=pi.purchase_id WHERE pi.product_id=$id AND pi.deleted_at IS NULL AND p.deleted_at IS NULL AND p.date BETWEEN '$first' AND '$last'");
            $keluar = $S("SELECT COALESCE(SUM(-qty),0) FROM sales_stock WHERE product_id=$id AND type='sale' AND created_at BETWEEN '$startTs' AND '$endTs'");
            $adjIn  = $S("SELECT COALESCE(SUM(qty),0) FROM stock_adjustments WHERE product_id=$id AND location='gudang' AND type='in' AND created_at BETWEEN '$startTs' AND '$endTs'")
                    + $S("SELECT COALESCE(SUM(qty),0) FROM sales_stock WHERE product_id=$id AND type='adjustment' AND qty>0 AND created_at BETWEEN '$startTs' AND '$endTs'");
            $adjOut = $S("SELECT COALESCE(SUM(qty),0) FROM stock_adjustments WHERE product_id=$id AND location='gudang' AND type='out' AND created_at BETWEEN '$startTs' AND '$endTs'")
                    + $S("SELECT COALESCE(SUM(-qty),0) FROM sales_stock WHERE product_id=$id AND type='adjustment' AND qty<0 AND created_at BETWEEN '$startTs' AND '$endTs'");
            list($gi, $go) = stock_adj_notes($conn, $id, 'gudang', $startTs, $endTs, 'gudang');
            list($ki, $ko) = stock_adj_notes($conn, $id, 'kantin', $startTs, $endTs, 'kantin');
            $inNotes  = array_merge($gi, $ki);
            $outNotes = array_merge($go, $ko);
        }

        return [
            'awal' => max(0, $awal), 'masuk' => $masuk, 'keluar' => $keluar,
            'adj_in' => $adjIn, 'adj_out' => $adjOut,
            'adj_in_note' => implode("\n", $inNotes),
            'adj_out_note' => implode("\n", $outNotes),
            'akhir' => max(0, $awal + $masuk - $keluar + $adjIn - $adjOut)
        ];
    }
}

/* Catatan adjustment terpisah per arah, format per item:
   (+qty) [(lokasi)] (catatan) — banyak item ditulis per baris.
   $tag diisi (mis. 'gudang') saat mode gabungan supaya lokasinya jelas. */
if (!function_exists('stock_adj_notes')) {
    function stock_adj_notes($conn, $id, $location, $startTs, $endTs, $tag = '')
    {
        $in  = [];
        $out = [];

        $fmt = function ($qty, $note) use ($tag) {
            return '(' . ($qty >= 0 ? '+' : '-') . abs((int) $qty) . ')'
                . ($tag !== '' ? ' (' . $tag . ')' : '')
                . ($note !== '' ? ' (' . $note . ')' : '');
        };

        if ($location === 'kantin') {
            $q = mysqli_query($conn, "SELECT qty, note FROM sales_stock WHERE product_id=$id AND type='adjustment' AND created_at BETWEEN '$startTs' AND '$endTs' ORDER BY created_at DESC LIMIT 10");
            while ($q && $r = mysqli_fetch_assoc($q)) {
                $qv = (int) $r['qty'];
                if ($qv > 0) $in[]  = $fmt($qv, $r['note']);
                else         $out[] = $fmt($qv, $r['note']);
            }
        } else {
            $q = mysqli_query($conn, "SELECT type, qty, note FROM stock_adjustments WHERE product_id=$id AND location='gudang' AND created_at BETWEEN '$startTs' AND '$endTs' ORDER BY created_at DESC LIMIT 10");
            while ($q && $r = mysqli_fetch_assoc($q)) {
                $line = $fmt(($r['type'] === 'in' ? 1 : -1) * (int) $r['qty'], $r['note']);
                if ($r['type'] === 'in') $in[] = $line;
                else                     $out[] = $line;
            }
        }

        return [$in, $out];
    }
}
