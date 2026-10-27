<?php

include '../../../sessions/session.php';
include 'report-helper.php';

require '../../../vendor/phpoffice/phpexcel/Classes/PHPExcel.php';
require '../../../vendor/phpoffice/phpexcel/Classes/PHPExcel/IOFactory.php';

$tab        = isset($_GET['tab']) ? $_GET['tab'] : 'all';
$first_date = isset($_GET['first_date']) ? $_GET['first_date'] : '';
$last_date  = isset($_GET['last_date']) ? $_GET['last_date'] : '';
$productSelection = report_product_selection(isset($_GET['product_id']) ? $_GET['product_id'] : 'all');
$product_id = (int) $productSelection['id'];
$category   = isset($_GET['category']) ? $_GET['category'] : '';
$cashier_id = isset($_GET['cashier_id']) ? (int) $_GET['cashier_id'] : 0;
$limit      = isset($_GET['limit']) ? (int) $_GET['limit'] : 10;
$detail     = isset($_GET['detail']) ? (int) $_GET['detail'] : 0;

$escFirst = mysqli_real_escape_string($conn, $first_date);
$escLast  = mysqli_real_escape_string($conn, $last_date);

$tabLabels = [
    'summary'  => 'RINGKASAN KEUANGAN',
    'omzet'    => 'LAPORAN OMZET HARIAN',
    'expense'  => 'LAPORAN PENGELUARAN',
    'purchase' => 'LAPORAN PEMBELIAN',
    'profit'   => 'LAPORAN LABA & RUGI',
    'margin'   => 'LAPORAN MARGIN PRODUK',
    'all'      => 'LAPORAN SEMUA TRANSAKSI',
    'product'  => 'LAPORAN PENJUALAN PER PRODUK',
    'category' => 'LAPORAN PENJUALAN PER KATEGORI',
    'cashier'  => 'LAPORAN PENJUALAN PER KASIR',
    'best'     => 'LAPORAN PRODUK TERLARIS',
    'unpaid'   => 'LAPORAN PIUTANG',
    'stock'    => 'LAPORAN STOK'
];

$tabTitles = [
    'summary'  => 'Ringkasan Keuangan',
    'omzet'    => 'Laporan Omzet Harian',
    'expense'  => 'Laporan Pengeluaran',
    'purchase' => 'Laporan Pembelian',
    'profit'   => 'Laporan Laba & Rugi',
    'margin'   => 'Laporan Margin Produk',
    'all'      => 'Laporan Semua Transaksi',
    'product'  => 'Laporan Penjualan Per Produk',
    'category' => 'Laporan Penjualan Per Kategori',
    'cashier'  => 'Laporan Penjualan Per Kasir',
    'best'     => 'Laporan Produk Terlaris',
    'unpaid'   => 'Laporan Piutang',
    'stock'    => 'Laporan Stok'
];

$title  = isset($tabLabels[$tab]) ? $tabLabels[$tab] : 'LAPORAN PENJUALAN';
$title2 = isset($tabTitles[$tab]) ? $tabTitles[$tab] : 'Laporan Penjualan';

$where = "DATE(tanggal) BETWEEN '$escFirst' AND '$escLast'";

$rows  = [];
$total = 0;
$extra = [];

switch ($tab) {

    case 'summary':
        $omzet = (float) mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(total),0) AS v FROM orders WHERE status_payment='paid' AND $where"))['v'];
        $expense = (float) mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(pi.price_buy),0) AS v FROM purchase_items pi JOIN purchases p ON pi.purchase_id=p.id WHERE p.deleted_at IS NULL AND pi.deleted_at IS NULL AND DATE(p.date) BETWEEN '$escFirst' AND '$escLast'"))['v'];
        $orderCount = (int) mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS v FROM orders WHERE status_payment='paid' AND $where"))['v'];
        $profit = $omzet - $expense;
        $total = $profit;
        $extra = compact('omzet', 'expense', 'orderCount', 'profit');
        break;

    case 'omzet':
        $q = mysqli_query($conn, "SELECT DATE(tanggal) AS dt, COUNT(*) AS total_order, SUM(CASE WHEN status_payment='paid' THEN 1 ELSE 0 END) AS paid_count, SUM(CASE WHEN status_payment='waiting' THEN 1 ELSE 0 END) AS waiting_count, SUM(CASE WHEN status_payment='cancelled' THEN 1 ELSE 0 END) AS cancelled_count, SUM(CASE WHEN status_payment='paid' THEN total ELSE 0 END) AS omzet FROM orders WHERE $where GROUP BY DATE(tanggal) ORDER BY dt ASC");
        if ($q) while ($r = mysqli_fetch_assoc($q)) { $rows[] = $r; $total += (float) $r['omzet']; }
        break;

    case 'expense':
        $q = mysqli_query($conn, "SELECT p.id, p.form, p.date, COUNT(pi.id) AS total_items, COALESCE(SUM(pi.price_buy),0) AS total_price FROM purchases p LEFT JOIN purchase_items pi ON p.id=pi.purchase_id AND pi.deleted_at IS NULL WHERE p.deleted_at IS NULL AND DATE(p.date) BETWEEN '$escFirst' AND '$escLast' AND EXISTS (SELECT 1 FROM purchase_items pi2 WHERE pi2.purchase_id=p.id AND pi2.deleted_at IS NULL AND pi2.qty_buy IS NOT NULL) GROUP BY p.id, p.form, p.date ORDER BY p.date ASC, p.id ASC");
        if ($q) while ($r = mysqli_fetch_assoc($q)) { $rows[] = $r; $total += (float) $r['total_price']; }
        break;

    case 'purchase':
        $purchase_id = isset($_GET['purchase_id']) ? (int) $_GET['purchase_id'] : 0;
        if ($purchase_id > 0) {
            $qPurchase = mysqli_query($conn, "SELECT p.id, p.date, p.form FROM purchases p WHERE p.id = $purchase_id AND p.deleted_at IS NULL LIMIT 1");
            $purchase = $qPurchase ? mysqli_fetch_assoc($qPurchase) : null;
            if ($purchase) {
                $q = mysqli_query($conn, "SELECT pi.id, pi.product_id, pi.qty_buy, pi.price_unit, pi.price_buy, p.code AS product_code, p.name AS product_name, p.unit AS product_unit FROM purchase_items pi JOIN products p ON pi.product_id = p.id WHERE pi.purchase_id = $purchase_id AND pi.deleted_at IS NULL AND pi.qty_buy IS NOT NULL ORDER BY pi.id ASC");
                if ($q) {
                    while ($r = mysqli_fetch_assoc($q)) {
                        $r['purchase_date'] = $purchase['date'];
                        $r['purchase_form'] = $purchase['form'];
                        $r['purchase_id'] = $purchase['id'];
                        $rows[] = $r;
                        $total += (float) $r['price_buy'];
                    }
                }
            }
        }
        break;

    case 'profit':
        $q = mysqli_query($conn, "SELECT d.dt, COALESCE(o.omzet,0) AS omzet, COALESCE(e.expense,0) AS expense FROM (SELECT DATE(tanggal) AS dt FROM orders WHERE $where UNION SELECT DATE(date) AS dt FROM purchases WHERE deleted_at IS NULL AND DATE(date) BETWEEN '$escFirst' AND '$escLast') d LEFT JOIN (SELECT DATE(tanggal) AS dt, COALESCE(SUM(total),0) AS omzet FROM orders WHERE status_payment='paid' AND $where GROUP BY DATE(tanggal)) o ON o.dt=d.dt LEFT JOIN (SELECT DATE(p.date) AS dt, COALESCE(SUM(pi.price_buy),0) AS expense FROM purchases p JOIN purchase_items pi ON p.id=pi.purchase_id WHERE p.deleted_at IS NULL AND pi.deleted_at IS NULL AND DATE(p.date) BETWEEN '$escFirst' AND '$escLast' GROUP BY DATE(p.date)) e ON e.dt=d.dt ORDER BY d.dt ASC");
        if ($q) while ($r = mysqli_fetch_assoc($q)) { $r['profit'] = (float)$r['omzet'] - (float)$r['expense']; $rows[] = $r; $total += $r['profit']; }
        break;

    case 'margin':
        $q = mysqli_query($conn, "SELECT p.name, COALESCE(SUM(od.qty),0) AS qty_sold, COALESCE(AVG(od.price),0) AS sell_price, COALESCE((SELECT AVG(pi.price_unit) FROM purchase_items pi WHERE pi.product_id=p.id AND pi.deleted_at IS NULL AND pi.price_unit>0),0) AS price, COALESCE(SUM(od.subtotal),0) AS revenue FROM order_details od JOIN orders o ON od.order_id=o.id JOIN products p ON od.product_id=p.id WHERE o.status_payment='paid' AND $where GROUP BY p.id, p.name ORDER BY revenue DESC, p.name ASC");
        if ($q) while ($r = mysqli_fetch_assoc($q)) { $qty=(int)$r['qty_sold']; $sell=(float)$r['sell_price']; $buy=(float)$r['price']; $r['margin_pct']=$sell>0?(($sell-$buy)/$sell)*100:0; $r['profit']=($sell-$buy)*$qty; $rows[]=$r; $total+=$r['profit']; }
        break;

    case 'all':
        $q = mysqli_query($conn, "SELECT o.code, o.tanggal, o.total, o.status_payment, COALESCE(NULLIF(u.fullname,''),u.username,'-') AS cashier_name FROM orders o LEFT JOIN users u ON o.staff_id=u.id WHERE $where ORDER BY o.tanggal ASC, o.id ASC");
        if ($q) while ($r = mysqli_fetch_assoc($q)) { if ($r['status_payment']==='paid') $total+=(float)$r['total']; $rows[]=$r; }
        break;

    case 'product':
        $range = "o.status_payment='paid' AND DATE(o.tanggal) BETWEEN '$escFirst' AND '$escLast'";

        $normalSelect = "
            SELECT o.code, o.tanggal, od.qty, od.price, od.subtotal,
                   p.name AS item_name, COALESCE(p.code, '') AS item_code,
                   0 AS is_combo, NULL AS combo_items
            FROM order_details od
            JOIN orders o ON od.order_id = o.id
            JOIN products p ON od.product_id = p.id
        ";

        $comboSelect = "
            SELECT o.code, o.tanggal,
                   MIN(FLOOR(od.qty / COALESCE(NULLIF(pci.qty, 0), 1))) AS qty,
                   ROUND(SUM(od.subtotal) / NULLIF(MIN(FLOOR(od.qty / COALESCE(NULLIF(pci.qty, 0), 1))), 0)) AS price,
                   SUM(od.subtotal) AS subtotal,
                   pc.name AS item_name,
                   '-' AS item_code,
                   1 AS is_combo,
                   GROUP_CONCAT(
                       CONCAT(ip.name, IF(pci.qty > 1, CONCAT(' x', pci.qty), ''))
                       ORDER BY pci.id SEPARATOR '||'
                   ) AS combo_items
            FROM order_details od
            JOIN orders o ON od.order_id = o.id
            JOIN product_combos pc ON pc.id = od.product_combo_id
            LEFT JOIN product_combo_items pci ON pci.combo_id = od.product_combo_id AND pci.product_id = od.product_id
            LEFT JOIN products ip ON ip.id = pci.product_id
        ";

        if ($productSelection['type'] === 'combo') {
            $q = mysqli_query($conn, "
                $comboSelect
                WHERE $range AND od.product_combo_id = $product_id
                GROUP BY od.order_id, od.product_combo_id, o.code, o.tanggal, pc.name
                ORDER BY o.tanggal ASC, o.code ASC
            ");
        } elseif ($productSelection['type'] === 'product') {
            $q = mysqli_query($conn, "
                $normalSelect
                WHERE $range AND od.product_id = $product_id
                ORDER BY o.tanggal ASC, o.id ASC
            ");
        } else {
            $q = mysqli_query($conn, "
                SELECT * FROM (
                    $normalSelect
                    WHERE $range AND od.product_combo_id IS NULL
                    UNION ALL
                    $comboSelect
                    WHERE $range
                    GROUP BY od.order_id, od.product_combo_id, o.code, o.tanggal, pc.name
                ) t
                ORDER BY t.tanggal ASC, t.code ASC
            ");
        }

        if ($q) while ($r = mysqli_fetch_assoc($q)) { $total+=(float)$r['subtotal']; $rows[]=$r; }
        break;

    case 'category':
        $range = "o.status_payment='paid' AND DATE(o.tanggal) BETWEEN '$escFirst' AND '$escLast'";
        $isAllCategory = ($category === '' || strtolower($category) === 'all');
        $isComboCategory = (strtolower($category) === 'racikan');

        if (!$isComboCategory) {
            $escCat = mysqli_real_escape_string($conn, $category);
            $catFilter = $isAllCategory ? '' : "AND p.category='$escCat'";

            $q = mysqli_query($conn, "SELECT p.name, p.code, COALESCE(SUM(od.qty),0) AS qty_sold, COALESCE(SUM(od.subtotal),0) AS omzet, MIN(o.tanggal) AS first_sold, 0 AS is_combo, '' AS combo_items FROM order_details od JOIN orders o ON od.order_id=o.id JOIN products p ON od.product_id=p.id WHERE $range $catFilter GROUP BY p.id, p.name, p.code");
            if ($q) while ($r = mysqli_fetch_assoc($q)) { $total+=(float)$r['omzet']; $rows[]=$r; }
        }

        if ($isAllCategory || $isComboCategory) {
            foreach (report_combo_sales($conn, $range) as $r) {
                $total += (float) $r['omzet'];
                $rows[] = $r;
            }
        }

        report_sort_by_first_sold($rows);
        break;

    case 'cashier':
        $q = mysqli_query($conn, "SELECT o.code, o.tanggal, o.total, o.status_payment FROM orders o WHERE o.staff_id=$cashier_id AND $where ORDER BY o.tanggal ASC, o.id ASC");
        if ($q) while ($r = mysqli_fetch_assoc($q)) { if ($r['status_payment']==='paid') $total+=(float)$r['total']; $rows[]=$r; }
        break;

    case 'best':
        if ($limit <= 0) $limit = 10;
        $q = mysqli_query($conn, "SELECT p.name, p.category, COALESCE(SUM(od.qty),0) AS qty_sold, COALESCE(SUM(od.subtotal),0) AS omzet FROM order_details od JOIN orders o ON od.order_id=o.id JOIN products p ON od.product_id=p.id WHERE o.status_payment='paid' AND $where GROUP BY p.id, p.name, p.category ORDER BY qty_sold DESC, omzet DESC LIMIT $limit");
        if ($q) while ($r = mysqli_fetch_assoc($q)) { $total+=(int)$r['qty_sold']; $rows[]=$r; }
        break;

    case 'unpaid':
        $q = mysqli_query($conn, "SELECT o.code, o.tanggal, o.total, COALESCE(NULLIF(u.fullname,''),u.username,'-') AS cashier_name FROM orders o LEFT JOIN users u ON o.staff_id=u.id WHERE o.status_payment='waiting' AND $where ORDER BY o.tanggal ASC, o.id ASC");
        if ($q) while ($r = mysqli_fetch_assoc($q)) { $total+=(float)$r['total']; $rows[]=$r; }
        break;

    case 'stock':
        $location = isset($_GET['location']) ? $_GET['location'] : 'gudang';
        if (!in_array($location, ['gudang','kantin','gabungan'])) $location = 'gudang';
        $startTs = $escFirst . ' 00:00:00';
        $endTs   = $escLast . ' 23:59:59';

        $stockRows = [];
        $qp = mysqli_query($conn, "SELECT id, name, code, unit FROM products WHERE category != 'Additional' ORDER BY name ASC");
        while ($qp && $p = mysqli_fetch_assoc($qp)) $stockRows[] = $p;

        foreach ($stockRows as $p) {
            $pid = (int)$p['id'];
            $unit = strtoupper($p['unit'] ?: '');
            $m = stock_mutation_row($conn, $pid, $location, $escFirst, $escLast, $startTs, $endTs);
            $rows[] = [
                'name'     => $p['name'],
                'code'     => $p['code'],
                'unit'     => $unit,
                'awal'     => $m['awal'],
                'masuk'    => $m['masuk'],
                'keluar'   => $m['keluar'],
                'adj_in'   => $m['adj_in'],
                'adj_out'  => $m['adj_out'],
                'adj_in_note'  => $m['adj_in_note'],
                'adj_out_note' => $m['adj_out_note'],
                'akhir'    => $m['akhir'],
            ];
        }
        break;
}

$count = ($tab === 'summary') ? 3 : count($rows);


$cashierName = '';
if ($tab === 'cashier' && $cashier_id > 0) {
    $qCashierName = mysqli_query($conn, "SELECT COALESCE(NULLIF(fullname,''), username, '-') AS name FROM users WHERE id = $cashier_id LIMIT 1");
    if ($qCashierName && $cashierRow = mysqli_fetch_assoc($qCashierName)) {
        $cashierName = $cashierRow['name'];
    }
}

$productName = '';
if ($tab === 'product') {
    $productName = report_product_label($conn, $productSelection);
}

$categoryName = '';
if ($tab === 'category' && $category !== '') {
    if (strtolower($category) === 'all') {
        $categoryName = 'Semua Kategori';
    } elseif (strtolower($category) === 'racikan') {
        $categoryName = 'Racikan';
    } else {
        $categoryName = ucfirst($category);
    }
}

// ============================================================
// MODE DETAIL PER TANGGAL - KHUSUS EXPORT EXCEL TAB KATEGORI
// Menampilkan matriks Produk x Tanggal (Qty Terjual + Omzet),
// dengan kolom Total (semua tanggal) di paling kanan.
// ============================================================
if ($tab === 'category' && $detail === 1) {

    // Daftar tanggal dalam rentang (inklusif)
    $detailDates = array();
    $tsStart = strtotime($first_date);
    $tsEnd   = strtotime($last_date);
    if ($tsStart !== false && $tsEnd !== false && $tsStart <= $tsEnd) {
        for ($t = $tsStart; $t <= $tsEnd; $t += 86400) {
            $detailDates[] = date('Y-m-d', $t);
        }
    }

    $escCat = mysqli_real_escape_string($conn, $category);
    $catFilter = ($category !== '' && strtolower($category) !== 'all') ? "AND p.category='$escCat'" : '';

    // Semua produk sesuai filter kategori (termasuk yang tidak terjual -> bernilai 0)
    $products = array();
    $qp = mysqli_query($conn, "SELECT p.id, p.name, p.code FROM products p WHERE p.deleted_at IS NULL $catFilter ORDER BY p.name ASC");
    if ($qp) {
        while ($pr = mysqli_fetch_assoc($qp)) {
            $products[(int) $pr['id']] = $pr;
        }
    }

    // Matriks penjualan per produk per tanggal
    $matrix = array();
    $productTotals = array();
    $qm = mysqli_query($conn, "SELECT od.product_id, DATE(o.tanggal) AS dt, COALESCE(SUM(od.qty),0) AS qty, COALESCE(SUM(od.subtotal),0) AS omzet FROM order_details od JOIN orders o ON od.order_id=o.id JOIN products p ON od.product_id=p.id WHERE o.status_payment='paid' AND $where $catFilter GROUP BY od.product_id, DATE(o.tanggal)");
    if ($qm) {
        while ($mr = mysqli_fetch_assoc($qm)) {
            $pid = (int) $mr['product_id'];
            $dt  = $mr['dt'];
            $matrix[$pid][$dt] = array('qty' => (int) $mr['qty'], 'omzet' => (float) $mr['omzet']);
            if (!isset($productTotals[$pid])) {
                $productTotals[$pid] = array('qty' => 0, 'omzet' => 0);
            }
            $productTotals[$pid]['qty']   += (int) $mr['qty'];
            $productTotals[$pid]['omzet'] += (float) $mr['omzet'];
        }
    }

    $numDates = count($detailDates);
    $lastColIdx = 2 + ($numDates * 2) + 2; // A=No, B=Produk, C=Kode, lalu 2 kolom/tanggal, lalu 2 kolom Total
    $lastCol = PHPExcel_Cell::stringFromColumnIndex($lastColIdx);

    $objPHPExcel = new PHPExcel();
    $objPHPExcel->getProperties()->setCreator("Qieos")->setTitle($title2);

    $sheet = $objPHPExcel->setActiveSheetIndex(0);
    $sheet->setTitle("Laporan");

    $sheet->mergeCells("A1:{$lastCol}1");
    $sheet->setCellValue('A1', 'PT. SELARASGRIYA SARANA UTAMA');
    $sheet->mergeCells("A2:{$lastCol}2");
    $sheet->setCellValue('A2', 'Pasar Induk Surabaya Sidotopo');
    $sheet->mergeCells("A4:{$lastCol}4");
    $sheet->setCellValue('A4', $title);

    $periode = '';
    if (!empty($first_date) && !empty($last_date)) {
        $periode = 'Periode : ' . date('d M Y', strtotime($first_date)) . ' s/d ' . date('d M Y', strtotime($last_date));
    }
    if ($categoryName !== '') {
        $periode = trim($periode . '  |  Kategori : ' . $categoryName);
    }
    $sheet->mergeCells("A5:{$lastCol}5");
    $sheet->setCellValue('A5', $periode);

    // Header 2 baris: baris 7 = tanggal (merge 2 kolom), baris 8 = Qty Terjual / Omzet
    $hdrTop = 7;
    $hdrBot = 8;

    foreach (array('A' => 'No', 'B' => 'Produk', 'C' => 'Kode') as $col => $label) {
        $sheet->mergeCells("{$col}{$hdrTop}:{$col}{$hdrBot}");
        $sheet->setCellValue("{$col}{$hdrTop}", $label);
    }

    $qtyCols = array();
    $omzetCols = array();

    $col = 3;
    foreach ($detailDates as $d) {
        $c1 = PHPExcel_Cell::stringFromColumnIndex($col);
        $c2 = PHPExcel_Cell::stringFromColumnIndex($col + 1);
        $sheet->mergeCells("{$c1}{$hdrTop}:{$c2}{$hdrTop}");
        $sheet->setCellValue("{$c1}{$hdrTop}", date('d/m/Y', strtotime($d)));
        $sheet->setCellValue("{$c1}{$hdrBot}", 'Qty Terjual');
        $sheet->setCellValue("{$c2}{$hdrBot}", 'Omzet');
        $qtyCols[] = $c1;
        $omzetCols[] = $c2;
        $col += 2;
    }

    $cTot1 = PHPExcel_Cell::stringFromColumnIndex($col);
    $cTot2 = PHPExcel_Cell::stringFromColumnIndex($col + 1);
    $sheet->mergeCells("{$cTot1}{$hdrTop}:{$cTot2}{$hdrTop}");
    $sheet->setCellValue("{$cTot1}{$hdrTop}", 'Total');
    $sheet->setCellValue("{$cTot1}{$hdrBot}", 'Qty Terjual');
    $sheet->setCellValue("{$cTot2}{$hdrBot}", 'Omzet');
    $qtyCols[] = $cTot1;
    $omzetCols[] = $cTot2;

    $sheet->getStyle("A{$hdrTop}:{$lastCol}{$hdrBot}")->applyFromArray(array(
        'font' => array('bold' => true, 'color' => array('rgb' => 'FFFFFF')),
        'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '1E1B4B')),
        'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER, 'wrapText' => true),
        'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
    ));

    // Baris data (satu baris per produk)
    $row = $hdrBot + 1;
    $firstDataRow = $row;
    $no = 1;

    foreach ($products as $pid => $pr) {
        $sheet->setCellValue("A$row", $no);
        $sheet->setCellValue("B$row", $pr['name']);
        $sheet->setCellValue("C$row", $pr['code']);

        $col = 3;
        foreach ($detailDates as $d) {
            $c1 = PHPExcel_Cell::stringFromColumnIndex($col);
            $c2 = PHPExcel_Cell::stringFromColumnIndex($col + 1);
            $cell = isset($matrix[$pid][$d]) ? $matrix[$pid][$d] : array('qty' => 0, 'omzet' => 0);
            $sheet->setCellValue("{$c1}$row", $cell['qty']);
            $sheet->setCellValue("{$c2}$row", $cell['omzet']);
            $col += 2;
        }

        $tot = isset($productTotals[$pid]) ? $productTotals[$pid] : array('qty' => 0, 'omzet' => 0);
        $sheet->setCellValue("{$cTot1}$row", $tot['qty']);
        $sheet->setCellValue("{$cTot2}$row", $tot['omzet']);

        $row++;
        $no++;
    }
    $lastDataRow = $row - 1;

    // Baris total (jumlah seluruh produk per tanggal + grand total)
    $sheet->mergeCells("A$row:C$row");
    $sheet->setCellValue("A$row", 'TOTAL');
    $sheet->getStyle("A$row")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

    $col = 3;
    foreach ($detailDates as $d) {
        $c1 = PHPExcel_Cell::stringFromColumnIndex($col);
        $c2 = PHPExcel_Cell::stringFromColumnIndex($col + 1);
        if ($lastDataRow >= $firstDataRow) {
            $sheet->setCellValue("{$c1}$row", "=SUM({$c1}$firstDataRow:{$c1}$lastDataRow)");
            $sheet->setCellValue("{$c2}$row", "=SUM({$c2}$firstDataRow:{$c2}$lastDataRow)");
        } else {
            $sheet->setCellValue("{$c1}$row", 0);
            $sheet->setCellValue("{$c2}$row", 0);
        }
        $col += 2;
    }
    if ($lastDataRow >= $firstDataRow) {
        $sheet->setCellValue("{$cTot1}$row", "=SUM({$cTot1}$firstDataRow:{$cTot1}$lastDataRow)");
        $sheet->setCellValue("{$cTot2}$row", "=SUM({$cTot2}$firstDataRow:{$cTot2}$lastDataRow)");
    } else {
        $sheet->setCellValue("{$cTot1}$row", 0);
        $sheet->setCellValue("{$cTot2}$row", 0);
    }

    $sheet->getStyle("A$row:{$lastCol}$row")->applyFromArray(array(
        'font' => array('bold' => true, 'color' => array('rgb' => 'FFFFFF')),
        'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '1E1B4B')),
        'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
    ));

    // Border seluruh tabel + perataan
    $sheet->getStyle("A{$hdrTop}:{$lastCol}$row")->applyFromArray(array(
        'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
    ));
    $sheet->getStyle("A{$firstDataRow}:C$row")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle("B{$firstDataRow}:B$row")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

    foreach ($qtyCols as $c) {
        $sheet->getStyle("{$c}{$firstDataRow}:{$c}$row")
            ->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("{$c}{$firstDataRow}:{$c}$row")
            ->getNumberFormat()->setFormatCode('#,##0');
    }
    foreach ($omzetCols as $c) {
        $sheet->getStyle("{$c}{$firstDataRow}:{$c}$row")
            ->getNumberFormat()->setFormatCode('"Rp " #,##0');
    }

    // Freeze kolom produk + baris header
    $sheet->freezePane('D' . $firstDataRow);

    for ($ci = 0; $ci <= $lastColIdx; $ci++) {
        $cl = PHPExcel_Cell::stringFromColumnIndex($ci);
        $sheet->getColumnDimension($cl)->setAutoSize(true);
    }

    if (ob_get_length()) {
        ob_end_clean();
    }

    $filename = $title2 . ' (Detail) - ' . date('d M Y', strtotime($first_date)) . ' s.d. ' . date('d M Y', strtotime($last_date)) . '.xlsx';

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    header('Pragma: public');

    try {
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    } catch (Exception $e) {
        error_log('[QIEOS Excel Error] ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
        header('Content-Type: text/html; charset=utf-8');
        echo '<h3>Error Generating Excel</h3>';
        echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
    }

    exit;
}

// ============================================================
// MODE DETAIL PER TANGGAL - EXPORT EXCEL TAB RINGKASAN KEUANGAN
// Baris = komponen (Omzet, Pengeluaran, Laba), kolom = per tanggal + Total.
// ============================================================
if ($tab === 'summary' && $detail === 1) {

    $detailDates = array();
    $tsStart = strtotime($first_date);
    $tsEnd   = strtotime($last_date);
    if ($tsStart !== false && $tsEnd !== false && $tsStart <= $tsEnd) {
        for ($t = $tsStart; $t <= $tsEnd; $t += 86400) {
            $detailDates[] = date('Y-m-d', $t);
        }
    }

    $omzetByDate = array();
    $qo = mysqli_query($conn, "SELECT DATE(tanggal) AS dt, COALESCE(SUM(total),0) AS v FROM orders WHERE status_payment='paid' AND $where GROUP BY DATE(tanggal)");
    if ($qo) { while ($ro = mysqli_fetch_assoc($qo)) { $omzetByDate[$ro['dt']] = (float) $ro['v']; } }

    $expenseByDate = array();
    $qe = mysqli_query($conn, "SELECT DATE(p.date) AS dt, COALESCE(SUM(pi.price_buy),0) AS v FROM purchase_items pi JOIN purchases p ON pi.purchase_id=p.id WHERE p.deleted_at IS NULL AND pi.deleted_at IS NULL AND DATE(p.date) BETWEEN '$escFirst' AND '$escLast' GROUP BY DATE(p.date)");
    if ($qe) { while ($re = mysqli_fetch_assoc($qe)) { $expenseByDate[$re['dt']] = (float) $re['v']; } }

    $numDates = count($detailDates);
    $lastColIdx = 1 + $numDates + 1; // A=No, B=Komponen, tanggal..., Total
    $lastCol = PHPExcel_Cell::stringFromColumnIndex($lastColIdx);

    $objPHPExcel = new PHPExcel();
    $objPHPExcel->getProperties()->setCreator("Qieos")->setTitle($title2);

    $sheet = $objPHPExcel->setActiveSheetIndex(0);
    $sheet->setTitle("Laporan");

    $sheet->mergeCells("A1:{$lastCol}1");
    $sheet->setCellValue('A1', 'PT. SELARASGRIYA SARANA UTAMA');
    $sheet->mergeCells("A2:{$lastCol}2");
    $sheet->setCellValue('A2', 'Pasar Induk Surabaya Sidotopo');
    $sheet->mergeCells("A4:{$lastCol}4");
    $sheet->setCellValue('A4', $title);

    $periode = '';
    if (!empty($first_date) && !empty($last_date)) {
        $periode = 'Periode : ' . date('d M Y', strtotime($first_date)) . ' s/d ' . date('d M Y', strtotime($last_date));
    }
    $sheet->mergeCells("A5:{$lastCol}5");
    $sheet->setCellValue('A5', $periode);

    $hdrTop = 7;
    $hdrBot = 8;

    $sheet->mergeCells("A{$hdrTop}:A{$hdrBot}");
    $sheet->setCellValue("A{$hdrTop}", 'No');
    $sheet->mergeCells("B{$hdrTop}:B{$hdrBot}");
    $sheet->setCellValue("B{$hdrTop}", 'Komponen');

    $col = 2;
    foreach ($detailDates as $d) {
        $c = PHPExcel_Cell::stringFromColumnIndex($col);
        $sheet->mergeCells("{$c}{$hdrTop}:{$c}{$hdrBot}");
        $sheet->setCellValue("{$c}{$hdrTop}", date('d/m/Y', strtotime($d)));
        $col++;
    }
    $cTot = PHPExcel_Cell::stringFromColumnIndex($col);
    $sheet->mergeCells("{$cTot}{$hdrTop}:{$cTot}{$hdrBot}");
    $sheet->setCellValue("{$cTot}{$hdrTop}", 'Total');

    $sheet->getStyle("A{$hdrTop}:{$lastCol}{$hdrBot}")->applyFromArray(array(
        'font' => array('bold' => true, 'color' => array('rgb' => 'FFFFFF')),
        'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '1E1B4B')),
        'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER, 'wrapText' => true),
        'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
    ));

    $firstDateCol = 'C';
    $lastDateCol = ($numDates > 0) ? PHPExcel_Cell::stringFromColumnIndex($numDates + 1) : 'B';

    $row = $hdrBot + 1;
    $firstDataRow = $row;
    $omzetRow = $row;

    // Baris Omzet Penjualan
    $sheet->setCellValue("A$row", 1);
    $sheet->setCellValue("B$row", 'Omzet Penjualan');
    $col = 2;
    foreach ($detailDates as $d) {
        $c = PHPExcel_Cell::stringFromColumnIndex($col);
        $sheet->setCellValue("{$c}$row", isset($omzetByDate[$d]) ? $omzetByDate[$d] : 0);
        $col++;
    }
    $sheet->setCellValue("{$cTot}$row", ($numDates > 0) ? "=SUM({$firstDateCol}$row:{$lastDateCol}$row)" : 0);
    $row++;

    // Baris Pengeluaran Belanja
    $expenseRow = $row;
    $sheet->setCellValue("A$row", 2);
    $sheet->setCellValue("B$row", 'Pengeluaran Belanja');
    $col = 2;
    foreach ($detailDates as $d) {
        $c = PHPExcel_Cell::stringFromColumnIndex($col);
        $sheet->setCellValue("{$c}$row", isset($expenseByDate[$d]) ? $expenseByDate[$d] : 0);
        $col++;
    }
    $sheet->setCellValue("{$cTot}$row", ($numDates > 0) ? "=SUM({$firstDateCol}$row:{$lastDateCol}$row)" : 0);
    $row++;

    // Baris Laba Bersih (omzet - pengeluaran per tanggal)
    $labaRow = $row;
    $sheet->setCellValue("A$row", 3);
    $sheet->setCellValue("B$row", 'Laba Bersih');
    $col = 2;
    foreach ($detailDates as $d) {
        $c = PHPExcel_Cell::stringFromColumnIndex($col);
        $sheet->setCellValue("{$c}$row", "={$c}{$omzetRow}-{$c}{$expenseRow}");
        $col++;
    }
    $sheet->setCellValue("{$cTot}$row", ($numDates > 0) ? "=SUM({$firstDateCol}$row:{$lastDateCol}$row)" : 0);

    $sheet->getStyle("A{$hdrTop}:{$lastCol}$row")->applyFromArray(array(
        'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
    ));
    $sheet->getStyle("A{$firstDataRow}:A$row")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle("B{$firstDataRow}:B$row")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    $sheet->getStyle("{$firstDateCol}{$firstDataRow}:{$lastCol}$row")->getNumberFormat()->setFormatCode('"Rp " #,##0');
    $sheet->getStyle("{$cTot}{$firstDataRow}:{$cTot}$row")->getFont()->setBold(true);

    $sheet->freezePane('C' . $firstDataRow);

    for ($ci = 0; $ci <= $lastColIdx; $ci++) {
        $cl = PHPExcel_Cell::stringFromColumnIndex($ci);
        $sheet->getColumnDimension($cl)->setAutoSize(true);
    }

    if (ob_get_length()) {
        ob_end_clean();
    }

    $filename = $title2 . ' (Detail) - ' . date('d M Y', strtotime($first_date)) . ' s.d. ' . date('d M Y', strtotime($last_date)) . '.xlsx';

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    header('Pragma: public');

    try {
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    } catch (Exception $e) {
        error_log('[QIEOS Excel Error] ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
        header('Content-Type: text/html; charset=utf-8');
        echo '<h3>Error Generating Excel</h3>';
        echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
    }

    exit;
}

// ============================================================
// MODE DETAIL PER TANGGAL - EXPORT EXCEL TAB PRODUK TERLARIS
// Baris = produk top-N, kolom = per tanggal (Qty+Omzet) + Total.
// ============================================================
if ($tab === 'best' && $detail === 1) {

    if ($limit <= 0) { $limit = 10; }

    $detailDates = array();
    $tsStart = strtotime($first_date);
    $tsEnd   = strtotime($last_date);
    if ($tsStart !== false && $tsEnd !== false && $tsStart <= $tsEnd) {
        for ($t = $tsStart; $t <= $tsEnd; $t += 86400) {
            $detailDates[] = date('Y-m-d', $t);
        }
    }

    // Produk terlaris top-N pada rentang
    $products = array();
    $qp = mysqli_query($conn, "SELECT p.id, p.name, p.category, COALESCE(SUM(od.qty),0) AS qty_sold, COALESCE(SUM(od.subtotal),0) AS omzet FROM order_details od JOIN orders o ON od.order_id=o.id JOIN products p ON od.product_id=p.id WHERE o.status_payment='paid' AND $where GROUP BY p.id, p.name, p.category ORDER BY qty_sold DESC, omzet DESC LIMIT $limit");
    if ($qp) { while ($pr = mysqli_fetch_assoc($qp)) { $products[(int) $pr['id']] = $pr; } }

    $matrix = array();
    $productTotals = array();
    if (!empty($products)) {
        $ids = implode(',', array_map('intval', array_keys($products)));
        $qm = mysqli_query($conn, "SELECT od.product_id, DATE(o.tanggal) AS dt, COALESCE(SUM(od.qty),0) AS qty, COALESCE(SUM(od.subtotal),0) AS omzet FROM order_details od JOIN orders o ON od.order_id=o.id WHERE o.status_payment='paid' AND $where AND od.product_id IN ($ids) GROUP BY od.product_id, DATE(o.tanggal)");
        if ($qm) {
            while ($mr = mysqli_fetch_assoc($qm)) {
                $pid = (int) $mr['product_id'];
                $dt  = $mr['dt'];
                $matrix[$pid][$dt] = array('qty' => (int) $mr['qty'], 'omzet' => (float) $mr['omzet']);
                if (!isset($productTotals[$pid])) {
                    $productTotals[$pid] = array('qty' => 0, 'omzet' => 0);
                }
                $productTotals[$pid]['qty']   += (int) $mr['qty'];
                $productTotals[$pid]['omzet'] += (float) $mr['omzet'];
            }
        }
    }

    $numDates = count($detailDates);
    $lastColIdx = 2 + ($numDates * 2) + 2; // A=Peringkat, B=Produk, C=Kategori, 2 kolom/tanggal, 2 kolom Total
    $lastCol = PHPExcel_Cell::stringFromColumnIndex($lastColIdx);

    $objPHPExcel = new PHPExcel();
    $objPHPExcel->getProperties()->setCreator("Qieos")->setTitle($title2);

    $sheet = $objPHPExcel->setActiveSheetIndex(0);
    $sheet->setTitle("Laporan");

    $sheet->mergeCells("A1:{$lastCol}1");
    $sheet->setCellValue('A1', 'PT. SELARASGRIYA SARANA UTAMA');
    $sheet->mergeCells("A2:{$lastCol}2");
    $sheet->setCellValue('A2', 'Pasar Induk Surabaya Sidotopo');
    $sheet->mergeCells("A4:{$lastCol}4");
    $sheet->setCellValue('A4', $title);

    $periode = '';
    if (!empty($first_date) && !empty($last_date)) {
        $periode = 'Periode : ' . date('d M Y', strtotime($first_date)) . ' s/d ' . date('d M Y', strtotime($last_date));
    }
    $sheet->mergeCells("A5:{$lastCol}5");
    $sheet->setCellValue('A5', $periode);

    $hdrTop = 7;
    $hdrBot = 8;

    foreach (array('A' => 'Peringkat', 'B' => 'Produk', 'C' => 'Kategori') as $cl => $label) {
        $sheet->mergeCells("{$cl}{$hdrTop}:{$cl}{$hdrBot}");
        $sheet->setCellValue("{$cl}{$hdrTop}", $label);
    }

    $qtyCols = array();
    $omzetCols = array();

    $col = 3;
    foreach ($detailDates as $d) {
        $c1 = PHPExcel_Cell::stringFromColumnIndex($col);
        $c2 = PHPExcel_Cell::stringFromColumnIndex($col + 1);
        $sheet->mergeCells("{$c1}{$hdrTop}:{$c2}{$hdrTop}");
        $sheet->setCellValue("{$c1}{$hdrTop}", date('d/m/Y', strtotime($d)));
        $sheet->setCellValue("{$c1}{$hdrBot}", 'Qty Terjual');
        $sheet->setCellValue("{$c2}{$hdrBot}", 'Omzet');
        $qtyCols[] = $c1;
        $omzetCols[] = $c2;
        $col += 2;
    }

    $cTot1 = PHPExcel_Cell::stringFromColumnIndex($col);
    $cTot2 = PHPExcel_Cell::stringFromColumnIndex($col + 1);
    $sheet->mergeCells("{$cTot1}{$hdrTop}:{$cTot2}{$hdrTop}");
    $sheet->setCellValue("{$cTot1}{$hdrTop}", 'Total');
    $sheet->setCellValue("{$cTot1}{$hdrBot}", 'Qty Terjual');
    $sheet->setCellValue("{$cTot2}{$hdrBot}", 'Omzet');
    $qtyCols[] = $cTot1;
    $omzetCols[] = $cTot2;

    $sheet->getStyle("A{$hdrTop}:{$lastCol}{$hdrBot}")->applyFromArray(array(
        'font' => array('bold' => true, 'color' => array('rgb' => 'FFFFFF')),
        'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '1E1B4B')),
        'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER, 'wrapText' => true),
        'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
    ));

    $row = $hdrBot + 1;
    $firstDataRow = $row;
    $no = 1;

    foreach ($products as $pid => $pr) {
        $sheet->setCellValue("A$row", $no);
        $sheet->setCellValue("B$row", $pr['name']);
        $sheet->setCellValue("C$row", ucfirst($pr['category']));

        $col = 3;
        foreach ($detailDates as $d) {
            $c1 = PHPExcel_Cell::stringFromColumnIndex($col);
            $c2 = PHPExcel_Cell::stringFromColumnIndex($col + 1);
            $cell = isset($matrix[$pid][$d]) ? $matrix[$pid][$d] : array('qty' => 0, 'omzet' => 0);
            $sheet->setCellValue("{$c1}$row", $cell['qty']);
            $sheet->setCellValue("{$c2}$row", $cell['omzet']);
            $col += 2;
        }

        $tot = isset($productTotals[$pid]) ? $productTotals[$pid] : array('qty' => 0, 'omzet' => 0);
        $sheet->setCellValue("{$cTot1}$row", $tot['qty']);
        $sheet->setCellValue("{$cTot2}$row", $tot['omzet']);

        $row++;
        $no++;
    }
    $lastDataRow = $row - 1;

    // Baris total
    $sheet->mergeCells("A$row:C$row");
    $sheet->setCellValue("A$row", 'TOTAL');
    $sheet->getStyle("A$row")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

    $col = 3;
    foreach ($detailDates as $d) {
        $c1 = PHPExcel_Cell::stringFromColumnIndex($col);
        $c2 = PHPExcel_Cell::stringFromColumnIndex($col + 1);
        if ($lastDataRow >= $firstDataRow) {
            $sheet->setCellValue("{$c1}$row", "=SUM({$c1}$firstDataRow:{$c1}$lastDataRow)");
            $sheet->setCellValue("{$c2}$row", "=SUM({$c2}$firstDataRow:{$c2}$lastDataRow)");
        } else {
            $sheet->setCellValue("{$c1}$row", 0);
            $sheet->setCellValue("{$c2}$row", 0);
        }
        $col += 2;
    }
    if ($lastDataRow >= $firstDataRow) {
        $sheet->setCellValue("{$cTot1}$row", "=SUM({$cTot1}$firstDataRow:{$cTot1}$lastDataRow)");
        $sheet->setCellValue("{$cTot2}$row", "=SUM({$cTot2}$firstDataRow:{$cTot2}$lastDataRow)");
    } else {
        $sheet->setCellValue("{$cTot1}$row", 0);
        $sheet->setCellValue("{$cTot2}$row", 0);
    }

    $sheet->getStyle("A$row:{$lastCol}$row")->applyFromArray(array(
        'font' => array('bold' => true, 'color' => array('rgb' => 'FFFFFF')),
        'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '1E1B4B')),
        'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
    ));

    $sheet->getStyle("A{$hdrTop}:{$lastCol}$row")->applyFromArray(array(
        'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
    ));
    $sheet->getStyle("A{$firstDataRow}:C$row")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle("B{$firstDataRow}:B$row")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

    foreach ($qtyCols as $c) {
        $sheet->getStyle("{$c}{$firstDataRow}:{$c}$row")
            ->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("{$c}{$firstDataRow}:{$c}$row")
            ->getNumberFormat()->setFormatCode('#,##0');
    }
    foreach ($omzetCols as $c) {
        $sheet->getStyle("{$c}{$firstDataRow}:{$c}$row")
            ->getNumberFormat()->setFormatCode('"Rp " #,##0');
    }

    $sheet->freezePane('D' . $firstDataRow);

    for ($ci = 0; $ci <= $lastColIdx; $ci++) {
        $cl = PHPExcel_Cell::stringFromColumnIndex($ci);
        $sheet->getColumnDimension($cl)->setAutoSize(true);
    }

    if (ob_get_length()) {
        ob_end_clean();
    }

    $filename = $title2 . ' (Detail) - ' . date('d M Y', strtotime($first_date)) . ' s.d. ' . date('d M Y', strtotime($last_date)) . '.xlsx';

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    header('Pragma: public');

    try {
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    } catch (Exception $e) {
        error_log('[QIEOS Excel Error] ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
        header('Content-Type: text/html; charset=utf-8');
        echo '<h3>Error Generating Excel</h3>';
        echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
    }

    exit;
}

// ============================
// CREATE EXCEL
// ============================
$objPHPExcel = new PHPExcel();
$objPHPExcel->getProperties()->setCreator("Qieos")->setTitle($title2);

$sheet = $objPHPExcel->setActiveSheetIndex(0);
$sheet->setTitle("Laporan");

$headers = [
    'summary'  => ['No', 'Komponen', 'Keterangan', 'Jumlah'],
    'omzet'    => ['No', 'Tanggal', 'Pesanan', 'Terbayar', 'Waiting', 'Cancel/Revisi', 'Omzet'],
    'expense'  => ['No', 'Tanggal', 'Form Belanja', 'Total Item', 'Total Belanja'],
    'purchase' => ['No', 'Tgl Beli', 'No Penerimaan Barang', 'No SJ/INV', 'Nama Supplier', 'Kode Barang', 'Nama Barang', 'Satuan', 'Barang Masuk/Qty', 'Total Pembayaran', 'Harga/Satuan'],
    'profit'   => ['No', 'Tanggal', 'Omzet', 'Pengeluaran', 'Laba / Rugi', 'Status'],
    'margin'   => ['No', 'Produk', 'Qty Terjual', 'Harga Beli', 'Harga Jual', 'Margin', 'Keuntungan'],
    'all'      => ['No', 'Kode Pesanan', 'Tanggal', 'Kasir', 'Total', 'Status'],
    'product'  => ['No', 'Produk', 'Tanggal', 'Kode Pesanan', 'Qty', 'Harga', 'Subtotal'],
    'category' => ['No', 'Produk', 'Kode', 'Qty Terjual', 'Omzet'],
    'cashier'  => ['No', 'Kode Pesanan', 'Tanggal', 'Total', 'Status'],
    'best'     => ['Peringkat', 'Produk', 'Kategori', 'Qty Terjual', 'Omzet'],
    'unpaid'   => ['No', 'Kode Pesanan', 'Tanggal', 'Kasir', 'Total', 'Status'],
    'stock'    => ['No', 'Nama Produk', 'Qty Awal', 'Qty Masuk', 'Qty Keluar', 'Adjustment Masuk (Penambahan)', 'Adjustment Keluar (Pengurangan)', 'Saldo Akhir']
];

$hdrs = isset($headers[$tab]) ? $headers[$tab] : $headers['all'];
$lastCol = chr(ord('A') + count($hdrs) - 1);

$sheet->mergeCells("A1:{$lastCol}1");
$sheet->setCellValue('A1', 'PT. SELARASGRIYA SARANA UTAMA');

$sheet->mergeCells("A2:{$lastCol}2");
$sheet->setCellValue('A2', 'Pasar Induk Surabaya Sidotopo');

$sheet->mergeCells("A4:{$lastCol}4");
$sheet->setCellValue('A4', $title);

$periode = '';
if (!empty($first_date) && !empty($last_date)) {
    $periode = 'Periode : ' . date('d M Y', strtotime($first_date)) . ' s/d ' . date('d M Y', strtotime($last_date));
}
if ($tab === 'cashier' && $cashierName !== '') {
    $periode = trim($periode . '  |  Kasir : ' . $cashierName);
}
if ($tab === 'product' && $productName !== '') {
    $periode = trim($periode . '  |  Produk : ' . $productName);
}
if ($tab === 'category' && $categoryName !== '') {
    $periode = trim($periode . '  |  Kategori : ' . $categoryName);
}
if ($tab === 'stock') {
    $locLabel = ['gudang' => 'Stok Gudang', 'kantin' => 'Stok Kantin', 'gabungan' => 'Gabungan (Gudang + Kantin)'];
    $periode = trim($periode . '  |  Lokasi : ' . (isset($locLabel[$location]) ? $locLabel[$location] : ucfirst($location)));
}
$sheet->mergeCells("A5:{$lastCol}5");
$sheet->setCellValue('A5', $periode);

$row = 7;
$colLetter = 'A';
foreach ($hdrs as $header) {
    $sheet->setCellValue($colLetter . $row, $header);
    $colLetter++;
}

$sheet->getStyle("A7:{$lastCol}7")->applyFromArray([
    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
    'fill' => ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => ['rgb' => '1E1B4B']],
    'alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER],
    'borders' => ['allborders' => ['style' => PHPExcel_Style_Border::BORDER_THIN]]
]);

$row = 8;
$no = 1;

for ($idx = 0; $idx < $count; $idx++) {

    $r = isset($rows[$idx]) ? $rows[$idx] : null;

    switch ($tab) {

        case 'summary':
            $summaryData = [
                ['Omzet Penjualan', $extra['orderCount'] . ' pesanan terbayar', $extra['omzet']],
                ['Pengeluaran Belanja', 'Dari daftar belanja stok', $extra['expense']],
                ['Laba Bersih', 'Omzet dikurangi pengeluaran', $extra['profit']]
            ];
            if (isset($summaryData[$no - 1])) {
                $sheet->setCellValue("A$row", $no);
                $sheet->setCellValue("B$row", $summaryData[$no - 1][0]);
                $sheet->setCellValue("C$row", $summaryData[$no - 1][1]);
                if ($no === 3) {
                    $sheet->setCellValue("D$row", '=D8-D9');
                } else {
                    $sheet->setCellValue("D$row", $summaryData[$no - 1][2]);
                }
            }
            break;

        case 'omzet':
            $sheet->setCellValue("A$row", $no);
            $sheet->setCellValue("B$row", date('d M Y', strtotime($r['dt'])));
            $sheet->setCellValue("C$row", (int) $r['total_order']);
            $sheet->setCellValue("D$row", (int) $r['paid_count']);
            $sheet->setCellValue("E$row", (int) $r['waiting_count']);
            $sheet->setCellValue("F$row", (int) $r['cancelled_count']);
            $sheet->setCellValue("G$row", (float) $r['omzet']);
            break;

        case 'expense':
            $sheet->setCellValue("A$row", $no);
            $sheet->setCellValue("B$row", date('d M Y', strtotime($r['date'])));
            $sheet->setCellValue("C$row", $r['form']);
            $sheet->setCellValue("D$row", (int) $r['total_items']);
            $sheet->setCellValue("E$row", (float) $r['total_price']);
            break;

        case 'purchase':
            $sheet->setCellValue("A$row", $no);
            $sheet->setCellValue("B$row", date('d M Y', strtotime($r['purchase_date'])));
            $sheet->setCellValue("C$row", (int) $r['purchase_id']);
            $sheet->setCellValue("D$row", '-');
            $sheet->setCellValue("E$row", '-');
            $sheet->setCellValue("F$row", $r['product_code']);
            $sheet->setCellValue("G$row", $r['product_name']);
            $sheet->setCellValue("H$row", strtoupper($r['product_unit']));
            $sheet->setCellValue("I$row", (int) $r['qty_buy']);
            $sheet->setCellValue("J$row", (float) $r['price_buy']);
            $sheet->setCellValue("K$row", (float) $r['price_unit']);
            break;

        case 'profit':
            $sheet->setCellValue("A$row", $no);
            $sheet->setCellValue("B$row", date('d M Y', strtotime($r['dt'])));
            $sheet->setCellValue("C$row", (float) $r['omzet']);
            $sheet->setCellValue("D$row", (float) $r['expense']);
            $sheet->setCellValue("E$row", "=C$row-D$row");
            $sheet->setCellValue("F$row", '=IF(E' . $row . '>=0,"Untung","Rugi")');
            break;

        case 'margin':
            $qty = (int) $r['qty_sold'];
            $sell = (float) $r['sell_price'];
            $buy = (float) $r['price'];
            $sheet->setCellValue("A$row", $no);
            $sheet->setCellValue("B$row", $r['name']);
            $sheet->setCellValue("C$row", $qty);
            $sheet->setCellValue("D$row", $buy);
            $sheet->setCellValue("E$row", $sell);
            $sheet->setCellValue("F$row", "=IF(E$row=0,0,(E$row-D$row)/E$row)");
            $sheet->setCellValue("G$row", "=(E$row-D$row)*C$row");
            break;

        case 'all':
            $sheet->setCellValue("A$row", $no);
            $sheet->setCellValue("B$row", $r['code']);
            $sheet->setCellValue("C$row", date('d M Y', strtotime($r['tanggal'])));
            $sheet->setCellValue("D$row", $r['cashier_name']);
            $sheet->setCellValue("E$row", (float) $r['total']);
            $sheet->setCellValue("F$row", $r['status_payment'] === 'paid' ? 'Terbayar' : ucfirst($r['status_payment']));
            break;

        case 'product':
            $productText = $r['item_name'] . (!empty($r['is_combo']) ? ' (Racikan)' : '');
            if (!empty($r['is_combo']) && !empty($r['combo_items'])) {
                $productText .= "\n" . implode("\n", array_filter(explode('||', $r['combo_items'])));
            }
            $sheet->setCellValue("A$row", $no);
            $sheet->setCellValue("B$row", $productText);
            $sheet->setCellValue("C$row", date('d M Y', strtotime($r['tanggal'])));
            $sheet->setCellValue("D$row", $r['code']);
            $sheet->setCellValue("E$row", (int) $r['qty']);
            $sheet->setCellValue("F$row", (float) $r['price']);
            $sheet->setCellValue("G$row", (float) $r['subtotal']);
            if (!empty($r['is_combo'])) {
                $sheet->getStyle("B$row")->getAlignment()->setWrapText(true);
            }
            $totalQty += (int) $r['qty'];
            break;

        case 'category':
            $categoryText = $r['name'] . (!empty($r['is_combo']) ? ' (Racikan)' : '');
            if (!empty($r['is_combo']) && !empty($r['combo_items'])) {
                $categoryText .= "\n" . implode("\n", array_filter(explode('||', $r['combo_items'])));
            }
            $sheet->setCellValue("A$row", $no);
            $sheet->setCellValue("B$row", $categoryText);
            $sheet->setCellValue("C$row", $r['code']);
            $sheet->setCellValue("D$row", (int) $r['qty_sold']);
            $sheet->setCellValue("E$row", (float) $r['omzet']);
            if (!empty($r['is_combo'])) {
                $sheet->getStyle("B$row")->getAlignment()->setWrapText(true);
                $sheet->getStyle("D$row")->getNumberFormat()->setFormatCode('0" (sudah diinclude ke qty produk asli)"');
            }
            $totalQty += (int) $r['qty_sold'];
            break;

        case 'cashier':
            $sheet->setCellValue("A$row", $no);
            $sheet->setCellValue("B$row", $r['code']);
            $sheet->setCellValue("C$row", date('d M Y', strtotime($r['tanggal'])));
            $sheet->setCellValue("D$row", (float) $r['total']);
            $sheet->setCellValue("E$row", $r['status_payment'] === 'paid' ? 'Terbayar' : ucfirst($r['status_payment']));
            break;

        case 'best':
            $sheet->setCellValue("A$row", $no);
            $sheet->setCellValue("B$row", $r['name']);
            $sheet->setCellValue("C$row", ucfirst($r['category']));
            $sheet->setCellValue("D$row", (int) $r['qty_sold']);
            $sheet->setCellValue("E$row", (float) $r['omzet']);
            break;

        case 'unpaid':
            $sheet->setCellValue("A$row", $no);
            $sheet->setCellValue("B$row", $r['code']);
            $sheet->setCellValue("C$row", date('d M Y', strtotime($r['tanggal'])));
            $sheet->setCellValue("D$row", $r['cashier_name']);
            $sheet->setCellValue("E$row", (float) $r['total']);
            $sheet->setCellValue("F$row", 'Belum Bayar');
            break;

        case 'stock':
            $sheet->setCellValue("A$row", $no);
            $sheet->setCellValue("B$row", $r['name']);
            $sheet->setCellValue("C$row", $r['awal'] . ($r['unit'] ? ' ' . $r['unit'] : ''));
            $sheet->setCellValue("D$row", $r['masuk'] . ($r['unit'] ? ' ' . $r['unit'] : ''));
            $sheet->setCellValue("E$row", $r['keluar'] . ($r['unit'] ? ' ' . $r['unit'] : ''));
            
            /* Format dari helper: "(+5) (Bonus 5 pcs)" per baris, banyak
               adjustment dipisah newline sehingga bertumpuk ke bawah. */
            $adjInText  = $r['adj_in_note']  !== '' ? $r['adj_in_note']  : '-';
            $adjOutText = $r['adj_out_note'] !== '' ? $r['adj_out_note'] : '-';
            
            $sheet->setCellValue("F$row", $adjInText);
            $sheet->getStyle("F$row")->getAlignment()->setWrapText(true);
            $sheet->setCellValue("G$row", $adjOutText);
            $sheet->getStyle("G$row")->getAlignment()->setWrapText(true);
            $sheet->setCellValue("H$row", $r['akhir'] . ($r['unit'] ? ' ' . $r['unit'] : ''));
            break;
    }

    $row++;
    $no++;
}

$totalLabels = [
    'summary'  => 'LABA BERSIH',
    'omzet'    => 'TOTAL OMZET',
    'expense'  => 'TOTAL PENGELUARAN',
    'purchase' => 'TOTAL PEMBAYARAN',
    'profit'   => 'TOTAL LABA / RUGI',
    'margin'   => 'TOTAL KEUNTUNGAN',
    'all'      => 'TOTAL OMZET (TERBAYAR)',
    'product'  => 'TOTAL',
    'category' => 'TOTAL',
    'cashier'  => 'TOTAL OMZET (TERBAYAR)',
    'best'     => 'TOTAL',
    'unpaid'   => 'TOTAL PIUTANG',
    'stock'    => ''
];
$totalLabel = isset($totalLabels[$tab]) ? $totalLabels[$tab] : 'TOTAL';

// Kolom angka yang dijumlah, sesuai urutan header di layar
$totalColMap = [
    'summary'  => 'D',
    'omzet'    => 'G',
    'expense'  => 'E',
    'purchase' => 'J',
    'profit'   => 'E',
    'margin'   => 'G',
    'all'      => 'E',
    'product'  => 'G',
    'category' => 'E',
    'cashier'  => 'D',
    'best'     => 'E',
    'unpaid'   => 'E'
];
$totalCol = isset($totalColMap[$tab]) ? $totalColMap[$tab] : 'D';
$totalColIdx = ord($totalCol) - ord('A');
$mergeEndCol = chr(ord('A') + max(0, $totalColIdx - 1));

$firstDataRow = 8;
$lastDataRow = $row - 1;
$hasDataRows = $lastDataRow >= $firstDataRow;

if ($tab !== 'stock') {

if ($tab === 'product') {
    $sheet->mergeCells("A$row:D$row");
    $sheet->setCellValue("A$row", $totalLabel);
    $sheet->getStyle("A$row")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    $sheet->setCellValue("E$row", $hasDataRows ? "=SUM(E$firstDataRow:E$lastDataRow)" : '=0');
    $sheet->setCellValue("G$row", $hasDataRows ? "=SUM(G$firstDataRow:G$lastDataRow)" : '=0');
} elseif ($tab === 'best' || $tab === 'category') {
    $sheet->mergeCells("A$row:C$row");
    $sheet->setCellValue("A$row", $totalLabel);
    $sheet->getStyle("A$row")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    $sheet->setCellValue("D$row", $hasDataRows ? "=SUM(D$firstDataRow:D$lastDataRow)" : '=0');
    $sheet->setCellValue("E$row", $hasDataRows ? "=SUM(E$firstDataRow:E$lastDataRow)" : '=0');
} else {
    $sheet->mergeCells("A$row:{$mergeEndCol}{$row}");
    $sheet->setCellValue("A$row", $totalLabel);
    $sheet->getStyle("A$row")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

    if (!$hasDataRows) {
        $formula = '=0';
    } elseif ($tab === 'summary') {
        $formula = '=D8-D9';
    } elseif ($tab === 'all') {
        $formula = "=SUMIF(F$firstDataRow:F$lastDataRow,\"Terbayar\",E$firstDataRow:E$lastDataRow)";
    } elseif ($tab === 'cashier') {
        $formula = "=SUMIF(E$firstDataRow:E$lastDataRow,\"Terbayar\",D$firstDataRow:D$lastDataRow)";
    } elseif ($tab === 'unpaid') {
        $formula = "=SUM(E$firstDataRow:E$lastDataRow)";
    } else {
        $formula = "=SUM($totalCol$firstDataRow:$totalCol$lastDataRow)";
    }

    $sheet->setCellValue($totalCol . $row, $formula);
}

$sheet->getStyle("A$row:{$lastCol}{$row}")->applyFromArray([
    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
    'fill' => ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => ['rgb' => '1E1B4B']],
    'borders' => ['allborders' => ['style' => PHPExcel_Style_Border::BORDER_THIN]]
]);

$goldTotal = [
    'font' => ['bold' => true, 'color' => ['rgb' => 'C4A35A']],
    'fill' => ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => ['rgb' => '1E1B4B']],
    'borders' => ['allborders' => ['style' => PHPExcel_Style_Border::BORDER_THIN]]
];

if ($tab === 'best' || $tab === 'category') {
    $sheet->getStyle("D$row")->applyFromArray($goldTotal);
    $sheet->getStyle("D$row")->getNumberFormat()->setFormatCode('#,##0');
    $sheet->getStyle("E$row")->applyFromArray($goldTotal);
    $sheet->getStyle("E$row")->getNumberFormat()->setFormatCode('"Rp " #,##0');
} elseif ($tab === 'product') {
    $sheet->getStyle("E$row")->applyFromArray($goldTotal);
    $sheet->getStyle("E$row")->getNumberFormat()->setFormatCode('#,##0');
    $sheet->getStyle("G$row")->applyFromArray($goldTotal);
    $sheet->getStyle("G$row")->getNumberFormat()->setFormatCode('"Rp " #,##0');
} else {
    $sheet->getStyle($totalCol . $row)->applyFromArray($goldTotal);
    $sheet->getStyle($totalCol . $row)->getNumberFormat()->setFormatCode('"Rp " #,##0');
}

}

$rpColMap = [
    'summary'  => ['D'],
    'omzet'    => ['F'],
    'expense'  => ['E'],
    'profit'   => ['C', 'D', 'E'],
    'margin'   => ['D', 'E', 'G'],
    'all'      => ['E'],
    'product'  => ['F', 'G'],
    'category' => ['E'],
    'cashier'  => ['D'],
    'best'     => ['E'],
    'unpaid'   => ['E'],
    'stock'    => []
];
$rpCols = isset($rpColMap[$tab]) ? $rpColMap[$tab] : ['D'];
if ($hasDataRows) {
    foreach ($rpCols as $c) {
        $sheet->getStyle("{$c}8:{$c}" . ($row - 1))->getNumberFormat()->setFormatCode('"Rp " #,##0');
    }
    if ($tab === 'margin') {
        $sheet->getStyle("F8:F" . ($row - 1))->getNumberFormat()->setFormatCode('0.0%');
    }
}

$sheet->getStyle("A7:{$lastCol}" . $row)->applyFromArray([
    'borders' => ['allborders' => ['style' => PHPExcel_Style_Border::BORDER_THIN]]
]);

$sheet->getStyle("A7:{$lastCol}" . $row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle("A7:A$row")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

foreach (range('A', $lastCol) as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// DOWNLOAD
if (ob_get_length()) {
    ob_end_clean();
}

$filename = $title2 . ' - ' . date('d M Y', strtotime($first_date)) . ' s.d. ' . date('d M Y', strtotime($last_date)) . '.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0');
header('Pragma: public');

try {
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('php://output');
} catch (Exception $e) {
    error_log('[QIEOS Excel Error] ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
    header('Content-Type: text/html; charset=utf-8');
    echo '<h3>Error Generating Excel</h3>';
    echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
    echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    exit;
}

exit;
