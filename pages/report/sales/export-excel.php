<?php

include '../../../sessions/session.php';

require '../../../vendor/phpoffice/phpexcel/Classes/PHPExcel.php';
require '../../../vendor/phpoffice/phpexcel/Classes/PHPExcel/IOFactory.php';

$tab        = isset($_GET['tab']) ? $_GET['tab'] : 'all';
$first_date = isset($_GET['first_date']) ? $_GET['first_date'] : '';
$last_date  = isset($_GET['last_date']) ? $_GET['last_date'] : '';
$product_id = isset($_GET['product_id']) ? (int) $_GET['product_id'] : 0;
$category   = isset($_GET['category']) ? $_GET['category'] : '';
$cashier_id = isset($_GET['cashier_id']) ? (int) $_GET['cashier_id'] : 0;
$limit      = isset($_GET['limit']) ? (int) $_GET['limit'] : 10;

$escFirst = mysqli_real_escape_string($conn, $first_date);
$escLast  = mysqli_real_escape_string($conn, $last_date);

$tabLabels = [
    'summary'  => 'RINGKASAN KEUANGAN',
    'omzet'    => 'LAPORAN OMZET HARIAN',
    'expense'  => 'LAPORAN PENGELUARAN',
    'profit'   => 'LAPORAN LABA & RUGI',
    'margin'   => 'LAPORAN MARGIN PRODUK',
    'all'      => 'LAPORAN SEMUA TRANSAKSI',
    'product'  => 'LAPORAN PENJUALAN PER PRODUK',
    'category' => 'LAPORAN PENJUALAN PER KATEGORI',
    'cashier'  => 'LAPORAN PENJUALAN PER KASIR',
    'best'     => 'LAPORAN PRODUK TERLARIS'
];

$tabTitles = [
    'summary'  => 'Ringkasan Keuangan',
    'omzet'    => 'Laporan Omzet Harian',
    'expense'  => 'Laporan Pengeluaran',
    'profit'   => 'Laporan Laba & Rugi',
    'margin'   => 'Laporan Margin Produk',
    'all'      => 'Laporan Semua Transaksi',
    'product'  => 'Laporan Penjualan Per Produk',
    'category' => 'Laporan Penjualan Per Kategori',
    'cashier'  => 'Laporan Penjualan Per Kasir',
    'best'     => 'Laporan Produk Terlaris'
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
        $q = mysqli_query($conn, "SELECT DATE(tanggal) AS dt, COUNT(*) AS total_order, SUM(CASE WHEN status_payment='paid' THEN 1 ELSE 0 END) AS paid_count, SUM(CASE WHEN status_payment='waiting' THEN 1 ELSE 0 END) AS waiting_count, SUM(CASE WHEN status_payment='paid' THEN total ELSE 0 END) AS omzet FROM orders WHERE $where GROUP BY DATE(tanggal) ORDER BY dt DESC");
        if ($q) while ($r = mysqli_fetch_assoc($q)) { $rows[] = $r; $total += (float) $r['omzet']; }
        break;

    case 'expense':
        $q = mysqli_query($conn, "SELECT p.id, p.form, p.date, COUNT(pi.id) AS total_items, COALESCE(SUM(pi.price_buy),0) AS total_price FROM purchases p LEFT JOIN purchase_items pi ON p.id=pi.purchase_id AND pi.deleted_at IS NULL WHERE p.deleted_at IS NULL AND DATE(p.date) BETWEEN '$escFirst' AND '$escLast' AND EXISTS (SELECT 1 FROM purchase_items pi2 WHERE pi2.purchase_id=p.id AND pi2.deleted_at IS NULL AND pi2.qty_buy IS NOT NULL) GROUP BY p.id, p.form, p.date ORDER BY p.date DESC, p.id DESC");
        if ($q) while ($r = mysqli_fetch_assoc($q)) { $rows[] = $r; $total += (float) $r['total_price']; }
        break;

    case 'profit':
        $q = mysqli_query($conn, "SELECT d.dt, COALESCE(o.omzet,0) AS omzet, COALESCE(e.expense,0) AS expense FROM (SELECT DATE(tanggal) AS dt FROM orders WHERE $where UNION SELECT DATE(date) AS dt FROM purchases WHERE deleted_at IS NULL AND DATE(date) BETWEEN '$escFirst' AND '$escLast') d LEFT JOIN (SELECT DATE(tanggal) AS dt, COALESCE(SUM(total),0) AS omzet FROM orders WHERE status_payment='paid' AND $where GROUP BY DATE(tanggal)) o ON o.dt=d.dt LEFT JOIN (SELECT DATE(p.date) AS dt, COALESCE(SUM(pi.price_buy),0) AS expense FROM purchases p JOIN purchase_items pi ON p.id=pi.purchase_id WHERE p.deleted_at IS NULL AND pi.deleted_at IS NULL AND DATE(p.date) BETWEEN '$escFirst' AND '$escLast' GROUP BY DATE(p.date)) e ON e.dt=d.dt ORDER BY d.dt DESC");
        if ($q) while ($r = mysqli_fetch_assoc($q)) { $r['profit'] = (float)$r['omzet'] - (float)$r['expense']; $rows[] = $r; $total += $r['profit']; }
        break;

    case 'margin':
        $q = mysqli_query($conn, "SELECT p.name, COALESCE(SUM(od.qty),0) AS qty_sold, COALESCE(AVG(od.price),0) AS sell_price, COALESCE((SELECT AVG(pi.price) FROM purchase_items pi WHERE pi.product_id=p.id AND pi.deleted_at IS NULL AND pi.price>0),0) AS price, COALESCE(SUM(od.subtotal),0) AS revenue FROM order_details od JOIN orders o ON od.order_id=o.id JOIN products p ON od.product_id=p.id WHERE o.status_payment='paid' AND $where GROUP BY p.id, p.name ORDER BY revenue DESC, p.name ASC");
        if ($q) while ($r = mysqli_fetch_assoc($q)) { $qty=(int)$r['qty_sold']; $sell=(float)$r['sell_price']; $buy=(float)$r['price']; $r['margin_pct']=$sell>0?(($sell-$buy)/$sell)*100:0; $r['profit']=($sell-$buy)*$qty; $rows[]=$r; $total+=$r['profit']; }
        break;

    case 'all':
        $q = mysqli_query($conn, "SELECT o.code, o.tanggal, o.total, o.status_payment, COALESCE(NULLIF(u.fullname,''),u.username,'-') AS cashier_name FROM orders o LEFT JOIN users u ON o.staff_id=u.id WHERE $where ORDER BY o.tanggal DESC, o.id DESC");
        if ($q) while ($r = mysqli_fetch_assoc($q)) { if ($r['status_payment']==='paid') $total+=(float)$r['total']; $rows[]=$r; }
        break;

    case 'product':
        $q = mysqli_query($conn, "SELECT o.code, o.tanggal, od.qty, od.price, od.subtotal FROM order_details od JOIN orders o ON od.order_id=o.id WHERE od.product_id=$product_id AND o.status_payment!='cancelled' AND $where ORDER BY o.tanggal DESC, o.id DESC");
        if ($q) while ($r = mysqli_fetch_assoc($q)) { $total+=(float)$r['subtotal']; $rows[]=$r; }
        break;

    case 'category':
        $escCat = mysqli_real_escape_string($conn, $category);
        $q = mysqli_query($conn, "SELECT p.name, p.code, COALESCE(SUM(od.qty),0) AS qty_sold, COALESCE(SUM(od.subtotal),0) AS omzet FROM order_details od JOIN orders o ON od.order_id=o.id JOIN products p ON od.product_id=p.id WHERE p.category='$escCat' AND o.status_payment='paid' AND $where GROUP BY p.id, p.name, p.code ORDER BY omzet DESC, p.name ASC");
        if ($q) while ($r = mysqli_fetch_assoc($q)) { $total+=(float)$r['omzet']; $rows[]=$r; }
        break;

    case 'cashier':
        $q = mysqli_query($conn, "SELECT o.code, o.tanggal, o.total, o.status_payment FROM orders o WHERE o.staff_id=$cashier_id AND $where ORDER BY o.tanggal DESC, o.id DESC");
        if ($q) while ($r = mysqli_fetch_assoc($q)) { if ($r['status_payment']==='paid') $total+=(float)$r['total']; $rows[]=$r; }
        break;

    case 'best':
        if ($limit <= 0) $limit = 10;
        $q = mysqli_query($conn, "SELECT p.name, p.category, COALESCE(SUM(od.qty),0) AS qty_sold, COALESCE(SUM(od.subtotal),0) AS omzet FROM order_details od JOIN orders o ON od.order_id=o.id JOIN products p ON od.product_id=p.id WHERE o.status_payment='paid' AND $where GROUP BY p.id, p.name, p.category ORDER BY qty_sold DESC, omzet DESC LIMIT $limit");
        if ($q) while ($r = mysqli_fetch_assoc($q)) { $total+=(int)$r['qty_sold']; $rows[]=$r; }
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
if ($tab === 'product' && $product_id > 0) {
    $qProductName = mysqli_query($conn, "SELECT COALESCE(name, '-') AS name FROM products WHERE id = $product_id LIMIT 1");
    if ($qProductName && $productRow = mysqli_fetch_assoc($qProductName)) {
        $productName = $productRow['name'];
    }
}

$categoryName = '';
if ($tab === 'category' && $category !== '') {
    $categoryName = ucfirst($category);
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
    'omzet'    => ['No', 'Tanggal', 'Pesanan', 'Terbayar', 'Waiting', 'Omzet'],
    'expense'  => ['No', 'Tanggal', 'Form Belanja', 'Total Item', 'Total Belanja'],
    'profit'   => ['No', 'Tanggal', 'Omzet', 'Pengeluaran', 'Laba / Rugi', 'Status'],
    'margin'   => ['No', 'Produk', 'Qty Terjual', 'Harga Beli', 'Harga Jual', 'Margin', 'Keuntungan'],
    'all'      => ['No', 'Kode Pesanan', 'Tanggal', 'Kasir', 'Total', 'Status'],
    'product'  => ['No', 'Tanggal', 'Kode Pesanan', 'Qty', 'Harga', 'Subtotal'],
    'category' => ['No', 'Produk', 'Kode', 'Qty Terjual', 'Omzet'],
    'cashier'  => ['No', 'Kode Pesanan', 'Tanggal', 'Total', 'Status'],
    'best'     => ['Peringkat', 'Produk', 'Kategori', 'Qty Terjual', 'Omzet']
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
            $sheet->setCellValue("F$row", (float) $r['omzet']);
            break;

        case 'expense':
            $sheet->setCellValue("A$row", $no);
            $sheet->setCellValue("B$row", date('d M Y', strtotime($r['date'])));
            $sheet->setCellValue("C$row", $r['form']);
            $sheet->setCellValue("D$row", (int) $r['total_items']);
            $sheet->setCellValue("E$row", (float) $r['total_price']);
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
            $sheet->setCellValue("A$row", $no);
            $sheet->setCellValue("B$row", date('d M Y', strtotime($r['tanggal'])));
            $sheet->setCellValue("C$row", $r['code']);
            $sheet->setCellValue("D$row", (int) $r['qty']);
            $sheet->setCellValue("E$row", (float) $r['price']);
            $sheet->setCellValue("F$row", (float) $r['subtotal']);
            $totalQty += (int) $r['qty'];
            break;

        case 'category':
            $sheet->setCellValue("A$row", $no);
            $sheet->setCellValue("B$row", $r['name']);
            $sheet->setCellValue("C$row", $r['code']);
            $sheet->setCellValue("D$row", (int) $r['qty_sold']);
            $sheet->setCellValue("E$row", (float) $r['omzet']);
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
    }

    $row++;
    $no++;
}

$totalLabels = [
    'summary'  => 'LABA BERSIH',
    'omzet'    => 'TOTAL OMZET',
    'expense'  => 'TOTAL PENGELUARAN',
    'profit'   => 'TOTAL LABA / RUGI',
    'margin'   => 'TOTAL KEUNTUNGAN',
    'all'      => 'TOTAL OMZET (TERBAYAR)',
    'product'  => 'TOTAL',
    'category' => 'TOTAL',
    'cashier'  => 'TOTAL OMZET (TERBAYAR)',
    'best'     => 'TOTAL'
];
$totalLabel = isset($totalLabels[$tab]) ? $totalLabels[$tab] : 'TOTAL';

// Kolom angka yang dijumlah, sesuai urutan header di layar
$totalColMap = [
    'summary'  => 'D',
    'omzet'    => 'F',
    'expense'  => 'E',
    'profit'   => 'E',
    'margin'   => 'G',
    'all'      => 'E',
    'product'  => 'F',
    'category' => 'E',
    'cashier'  => 'D',
    'best'     => 'E'
];
$totalCol = isset($totalColMap[$tab]) ? $totalColMap[$tab] : 'D';
$totalColIdx = ord($totalCol) - ord('A');
$mergeEndCol = chr(ord('A') + max(0, $totalColIdx - 1));

$firstDataRow = 8;
$lastDataRow = $row - 1;
$hasDataRows = $lastDataRow >= $firstDataRow;

if ($tab === 'best' || $tab === 'category' || $tab === 'product') {
    $sheet->mergeCells("A$row:C$row");
    $sheet->setCellValue("A$row", $totalLabel);
    $sheet->getStyle("A$row")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    if ($tab === 'product') {
        $sheet->setCellValue("D$row", $hasDataRows ? "=SUM(D$firstDataRow:D$lastDataRow)" : '=0');
        $sheet->setCellValue("F$row", $hasDataRows ? "=SUM(F$firstDataRow:F$lastDataRow)" : '=0');
    } else {
        $sheet->setCellValue("D$row", $hasDataRows ? "=SUM(D$firstDataRow:D$lastDataRow)" : '=0');
        $sheet->setCellValue("E$row", $hasDataRows ? "=SUM(E$firstDataRow:E$lastDataRow)" : '=0');
    }
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
    $sheet->getStyle("D$row")->applyFromArray($goldTotal);
    $sheet->getStyle("D$row")->getNumberFormat()->setFormatCode('#,##0');
    $sheet->getStyle("F$row")->applyFromArray($goldTotal);
    $sheet->getStyle("F$row")->getNumberFormat()->setFormatCode('"Rp " #,##0');
} else {
    $sheet->getStyle($totalCol . $row)->applyFromArray($goldTotal);
    $sheet->getStyle($totalCol . $row)->getNumberFormat()->setFormatCode('"Rp " #,##0');
}

$rpColMap = [
    'summary'  => ['D'],
    'omzet'    => ['F'],
    'expense'  => ['E'],
    'profit'   => ['C', 'D', 'E'],
    'margin'   => ['D', 'E', 'G'],
    'all'      => ['E'],
    'product'  => ['E', 'F'],
    'category' => ['E'],
    'cashier'  => ['D'],
    'best'     => ['E']
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
