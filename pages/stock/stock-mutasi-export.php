<?php
include '../../sessions/session.php';

require '../../vendor/phpoffice/phpexcel/Classes/PHPExcel.php';
require '../../vendor/phpoffice/phpexcel/Classes/PHPExcel/IOFactory.php';

include __DIR__ . '/stock-mutasi-data.php';

/* ============================================================
   Ekspor Excel panel "Mutasi Stok" (pages/stock/stock.php).

   Mengikuti TAB yang sedang dibuka di panel:
   - tab=gudang -> memakai $rowsG (mutasi stok gudang).
   - tab=kantin -> memakai $rowsK (mutasi stok kantin).
   Rentang tanggal mengikuti filter periode di panel. Desain sheet
   dibuat rapi + siap cetak di semua ukuran perangkat.
   ============================================================ */

$tab = (isset($_GET['tab']) && $_GET['tab'] === 'kantin') ? 'kantin' : 'gudang';

if(!$prod){
    header('Content-Type: text/plain; charset=utf-8', true, 404);
    echo 'Produk tidak ditemukan';
    exit;
}

$isKantin = ($tab === 'kantin');
$tabLabel = $isKantin ? 'KANTIN' : 'GUDANG';

$rows   = $isKantin ? $rowsK : $rowsG;
$qAwal  = $isKantin ? $kAwal  : $gAwal;
$qMasuk = $isKantin ? $kMasuk : $gMasuk;
$qKeluar= $isKantin ? $kKeluar: $gKeluar;
$qAkhir = $isKantin ? $kAkhir : $gAkhir;

$periode = mutasi_tgl($from, $bulan) . ' - ' . mutasi_tgl($to, $bulan);

/* --- Palet warna --- */
$cBrand   = '1E1B4B';
$cBrandSub= '312E81';
$cAccent  = '4F46E5';
$cAccentTl= 'EEF2FF';
$cLine    = 'E2E8F0';
$cText    = '1E293B';
$cMuted   = '64748B';
$cZebra   = 'F8FAFC';
$cIn      = '15803D';
$cOut     = 'B91C1C';
$cInBg    = 'ECFDF5';
$cOutBg   = 'FEF2F2';

$obj = new PHPExcel();
$obj->getProperties()
    ->setCreator('Qieos')
    ->setTitle('Mutasi Stok ' . $tabLabel)
    ->setDescription('Laporan mutasi stok ' . strtolower($tabLabel) . ' - ' . $prod['name']);
$obj->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

$sheet = $obj->setActiveSheetIndex(0);
$sheet->setTitle('Mutasi ' . ucfirst(strtolower($tabLabel)));
$sheet->setShowGridlines(false);

/* Lebar kolom (A..F) */
$widths = ['A' => 6, 'B' => 16, 'C' => 46, 'D' => 13, 'E' => 13, 'F' => 13];
foreach($widths as $col => $w) $sheet->getColumnDimension($col)->setWidth($w);

/* ---------------- Header brand ---------------- */
$sheet->mergeCells('A1:F1');
$sheet->setCellValue('A1', 'PT. SELARASGRIYA SARANA UTAMA');
$sheet->mergeCells('A2:F2');
$sheet->setCellValue('A2', 'Pasar Induk Surabaya Sidotopo');

$sheet->getStyle('A1:F1')->applyFromArray([
    'font'      => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
    'fill'      => ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => ['rgb' => $cBrand]],
    'alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER],
]);
$sheet->getStyle('A2:F2')->applyFromArray([
    'font'      => ['italic' => true, 'size' => 10, 'color' => ['rgb' => 'C7D2FE']],
    'fill'      => ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => ['rgb' => $cBrandSub]],
    'alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER],
]);
$sheet->getRowDimension(1)->setRowHeight(30);
$sheet->getRowDimension(2)->setRowHeight(20);
$sheet->getRowDimension(3)->setRowHeight(6);

/* ---------------- Judul laporan + info ---------------- */
$sheet->mergeCells('A4:F4');
$sheet->setCellValue('A4', 'LAPORAN MUTASI STOK ' . $tabLabel);
$sheet->getStyle('A4')->applyFromArray([
    'font'      => ['bold' => true, 'size' => 14, 'color' => ['rgb' => $cBrand]],
    'alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER],
]);
$sheet->getRowDimension(4)->setRowHeight(26);

$info = [
    5 => ['Produk', $prod['name'], 'Periode', $periode],
    6 => ['Kode', $prod['code'], 'Unit', $unit !== '' ? $unit : '-'],
    7 => ['Dicetak', date('d M Y H:i'), '', ''],
];
foreach($info as $row => $v){
    $sheet->mergeCells("B{$row}:C{$row}");
    $sheet->mergeCells("E{$row}:F{$row}");
    $sheet->setCellValue("A{$row}", $v[0]);
    $sheet->setCellValue("B{$row}", $v[1]);
    $sheet->setCellValue("D{$row}", $v[2]);
    $sheet->setCellValue("E{$row}", $v[3]);

    $sheet->getStyle("A{$row}")->applyFromArray([
        'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => $cMuted]],
        'alignment' => ['vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER],
    ]);
    $sheet->getStyle("D{$row}")->applyFromArray([
        'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => $cMuted]],
        'alignment' => ['vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER],
    ]);
    $sheet->getStyle("B{$row}:F{$row}")->applyFromArray([
        'font' => ['size' => 10, 'color' => ['rgb' => $cText]],
        'alignment' => ['vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER],
    ]);
}
$sheet->getRowDimension(8)->setRowHeight(8);

/* ---------------- Kartu ringkasan (2x2) ---------------- */
$cards = [
    [9,  'A', 'C', 'QTY AWAL',   $qAwal],
    [9,  'D', 'F', 'QTY MASUK',  $qMasuk],
    [11, 'A', 'C', 'QTY KELUAR', $qKeluar],
    [11, 'D', 'F', 'QTY AKHIR',  $qAkhir],
];
foreach($cards as $c){
    list($lr, $lc, $ec, $label, $value) = $c;
    $vr = $lr + 1;

    $sheet->mergeCells("{$lc}{$lr}:{$ec}{$lr}");
    $sheet->setCellValue("{$lc}{$lr}", $label);
    $sheet->getStyle("{$lc}{$lr}:{$ec}{$lr}")->applyFromArray([
        'font'      => ['bold' => true, 'size' => 10, 'color' => ['rgb' => $cAccent]],
        'fill'      => ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => ['rgb' => $cAccentTl]],
        'alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER],
        'borders'   => ['allborders' => ['style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => ['rgb' => $cLine]]],
    ]);

    $sheet->mergeCells("{$lc}{$vr}:{$ec}{$vr}");
    $sheet->setCellValue("{$lc}{$vr}", (int)$value);
    $sheet->getStyle("{$lc}{$vr}:{$ec}{$vr}")->applyFromArray([
        'font'      => ['bold' => true, 'size' => 14, 'color' => ['rgb' => $cText]],
        'fill'      => ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => ['rgb' => 'FFFFFF']],
        'alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER],
        'borders'   => ['allborders' => ['style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => ['rgb' => $cLine]]],
    ]);
    $sheet->getStyle("{$lc}{$vr}:{$ec}{$vr}")->getNumberFormat()->setFormatCode('#,##0');
    $sheet->getRowDimension($lr)->setRowHeight(18);
    $sheet->getRowDimension($vr)->setRowHeight(26);
}
$sheet->getRowDimension(13)->setRowHeight(8);

/* Unit diselipkan ke label kartu kalau ada (mis. "QTY MASUK (PCS)") */
if($unit !== ''){
    $sheet->setCellValue('A9',  'QTY AWAL ('   . $unit . ')');
    $sheet->setCellValue('D9',  'QTY MASUK ('  . $unit . ')');
    $sheet->setCellValue('A11', 'QTY KELUAR (' . $unit . ')');
    $sheet->setCellValue('D11', 'QTY AKHIR ('  . $unit . ')');
}

/* ---------------- Tabel riwayat ---------------- */
$headRow  = 14;
$dataRow  = 15;
$headers  = ['No', 'Tanggal', 'Keterangan', 'Masuk', 'Keluar', 'Saldo'];
$col      = 'A';
foreach($headers as $h){
    $sheet->setCellValue($col . $headRow, $h);
    $col++;
}
$sheet->getStyle("A{$headRow}:F{$headRow}")->applyFromArray([
    'font'      => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
    'fill'      => ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => ['rgb' => $cAccent]],
    'alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER],
    'borders'   => ['allborders' => ['style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => ['rgb' => $cAccent]]],
]);
$sheet->getRowDimension($headRow)->setRowHeight(22);

$no = 1;
$r  = $dataRow;
$sumMasuk  = 0;
$sumKeluar = 0;

if(!$rows){
    $sheet->mergeCells("A{$r}:F{$r}");
    $sheet->setCellValue("A{$r}", 'Tidak ada pergerakan stok pada rentang tanggal ini');
    $sheet->getStyle("A{$r}:F{$r}")->applyFromArray([
        'font'      => ['italic' => true, 'size' => 11, 'color' => ['rgb' => $cMuted]],
        'alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER],
        'borders'   => ['allborders' => ['style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => ['rgb' => $cLine]]],
    ]);
    $sheet->getRowDimension($r)->setRowHeight(24);
    $r++;
} else {
    foreach($rows as $i => $row){
        $ket = $row['title'];
        if(!empty($row['sub'])) $ket .= ' (' . $row['sub'] . ')';

        $sheet->setCellValue("A{$r}", $no++);
        $sheet->setCellValue("B{$r}", mutasi_tgl($row['tgl'], $bulan));
        $sheet->setCellValue("C{$r}", $ket);
        $sheet->setCellValue("D{$r}", $row['masuk']  > 0 ? (int)$row['masuk']  : '');
        $sheet->setCellValue("E{$r}", $row['keluar'] > 0 ? (int)$row['keluar'] : '');
        $sheet->setCellValue("F{$r}", (int)$row['saldo']);

        $sumMasuk  += (int)$row['masuk'];
        $sumKeluar += (int)$row['keluar'];

        $zebra = ($i % 2 === 1);

        $sheet->getStyle("A{$r}:F{$r}")->applyFromArray([
            'fill'      => ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => ['rgb' => $zebra ? $cZebra : 'FFFFFF']],
            'borders'   => ['allborders' => ['style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => ['rgb' => $cLine]]],
            'alignment' => ['vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getStyle("A{$r}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("B{$r}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("D{$r}:F{$r}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        if($row['masuk'] > 0){
            $sheet->getStyle("D{$r}")->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => $cIn]],
                'fill' => ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => ['rgb' => $cInBg]],
            ]);
        }
        if($row['keluar'] > 0){
            $sheet->getStyle("E{$r}")->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => $cOut]],
                'fill' => ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => ['rgb' => $cOutBg]],
            ]);
        }
        $sheet->getStyle("F{$r}")->getFont()->setBold(true);
        $sheet->getStyle("D{$r}:F{$r}")->getNumberFormat()->setFormatCode('#,##0');

        $sheet->getRowDimension($r)->setRowHeight(20);
        $r++;
    }
}

/* Baris total */
$totalRow = $r;
$sheet->mergeCells("A{$totalRow}:C{$totalRow}");
$sheet->setCellValue("A{$totalRow}", 'TOTAL PERGERAKAN');
$sheet->setCellValue("D{$totalRow}", $sumMasuk);
$sheet->setCellValue("E{$totalRow}", $sumKeluar);
$sheet->setCellValue("F{$totalRow}", $qAkhir);
$sheet->getStyle("A{$totalRow}:F{$totalRow}")->applyFromArray([
    'font'      => ['bold' => true, 'size' => 11, 'color' => ['rgb' => $cBrand]],
    'fill'      => ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => ['rgb' => $cAccentTl]],
    'borders'   => ['allborders' => ['style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => ['rgb' => 'C7D2FE']]],
    'alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER],
]);
$sheet->getStyle("D{$totalRow}:F{$totalRow}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle("D{$totalRow}:F{$totalRow}")->getNumberFormat()->setFormatCode('#,##0');
$sheet->getRowDimension($totalRow)->setRowHeight(22);

/* Catatan kaki */
$footRow = $totalRow + 2;
$sheet->mergeCells("A{$footRow}:F{$footRow}");
$sheet->setCellValue("A{$footRow}", 'Digenerate otomatis oleh Qieos  •  Saldo akhir gudang/kantin per ' . mutasi_tgl($to, $bulan));
$sheet->getStyle("A{$footRow}:F{$footRow}")->applyFromArray([
    'font'      => ['italic' => true, 'size' => 9, 'color' => ['rgb' => $cMuted]],
    'alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT],
]);

/* ---------------- Rapi untuk semua perangkat / cetak ---------------- */
$lastRow = $footRow;
$sheet->freezePane("A{$dataRow}");
$sheet->setAutoFilter("A{$headRow}:F{$headRow}");
$sheet->getPageSetup()
    ->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE)
    ->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4)
    ->setFitToPage(true)
    ->setFitToWidth(1)
    ->setFitToHeight(0)
    ->setRowsToRepeatAtTopByStartAndEnd($headRow, $headRow);
$sheet->getPageSetup()->setPrintArea("A1:F{$lastRow}");
$sheet->getPageMargins()->setTop(0.5)->setBottom(0.5)->setLeft(0.4)->setRight(0.4);
$sheet->getSheetView()->setZoomScale(100);

/* ---------------- Output ---------------- */
$safeName = preg_replace('/[^A-Za-z0-9 _-]+/', '', $prod['name']);
$safeName = trim(preg_replace('/\s+/', ' ', $safeName));
if($safeName === '') $safeName = 'Produk';
$filename = 'Mutasi Stok ' . ucfirst(strtolower($tabLabel)) . ' - ' . $safeName . ' - ' . date('d M Y') . '.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = PHPExcel_IOFactory::createWriter($obj, 'Excel2007');
$writer->save('php://output');
exit;
