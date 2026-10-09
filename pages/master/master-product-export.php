<?php
include '../../sessions/session.php';

require '../../vendor/phpoffice/phpexcel/Classes/PHPExcel.php';
require '../../vendor/phpoffice/phpexcel/Classes/PHPExcel/IOFactory.php';

$q = mysqli_query($conn, "SELECT * FROM products WHERE deleted_at IS NULL ORDER BY name ASC");

$objPHPExcel = new PHPExcel();
$objPHPExcel->getProperties()->setCreator('Qieos')->setTitle('Data Produk');

$sheet = $objPHPExcel->setActiveSheetIndex(0);
$sheet->setTitle('Data Produk');

// ============================
// HEADER LAPORAN
// ============================
$sheet->mergeCells('A1:H1');
$sheet->setCellValue('A1', 'PT. SELARASGRIYA SARANA UTAMA');
$sheet->mergeCells('A2:H2');
$sheet->setCellValue('A2', 'Pasar Induk Surabaya Sidotopo');
$sheet->mergeCells('A4:H4');
$sheet->setCellValue('A4', 'DATA PRODUK');
$sheet->mergeCells('A5:H5');
$sheet->setCellValue('A5', 'Per : ' . date('d M Y'));

$sheet->getStyle('A4')->applyFromArray(['font' => ['bold' => true, 'color' => ['rgb' => '1E1B4B']]]);
$sheet->getStyle('A5')->applyFromArray(['font' => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '64748B']]]);

// ============================
// TABLE HEADER (baris 7)
// ============================
$row = 7;
$headers = array('No', 'Kode', 'Nama Produk', 'Kategori', 'Harga Jual', 'Satuan', 'Batas Stok Gudang', 'Batas Stok Kantin');
$col = 'A';
foreach ($headers as $h) {
    $sheet->setCellValue($col . $row, $h);
    $col++;
}

$sheet->getStyle("A7:H7")->applyFromArray([
    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
    'fill' => ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => ['rgb' => '4F46E5']],
    'alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER],
    'borders' => ['allborders' => ['style' => PHPExcel_Style_Border::BORDER_THIN]],
]);

// ============================
// DATA
// ============================
$row = 8;
$no  = 1;
while ($data = mysqli_fetch_assoc($q)) {
    $sheet->setCellValue("A$row", $no++);
    $sheet->setCellValue("B$row", $data['code']);
    $sheet->setCellValue("C$row", $data['name']);
    $sheet->setCellValue("D$row", $data['category'] ?: '-');
    $sheet->setCellValue("E$row", $data['sell_price'] !== null ? (float)$data['sell_price'] : 0);
    $sheet->setCellValue("F$row", $data['unit'] ?: '');
    $sheet->setCellValue("G$row", ($data['low_stock'] !== null && $data['low_stock'] !== '') ? (int)$data['low_stock'] : '');
    $sheet->setCellValue("H$row", ($data['low_stock_kantin'] !== null && $data['low_stock_kantin'] !== '') ? (int)$data['low_stock_kantin'] : '');
    $row++;
}

$lastDataRow = $row - 1;

// border semua data
$sheet->getStyle("A7:H" . $lastDataRow)->applyFromArray([
    'borders' => ['allborders' => ['style' => PHPExcel_Style_Border::BORDER_THIN]],
]);

// alignment
$sheet->getStyle("A7:H" . $lastDataRow)
    ->getAlignment()
    ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle("A7:A" . $lastDataRow)
    ->getAlignment()
    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle("E7:E" . $lastDataRow)
    ->getAlignment()
    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$sheet->getStyle("G7:H" . $lastDataRow)
    ->getAlignment()
    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

// auto width
foreach (array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H') as $c) {
    $sheet->getColumnDimension($c)->setAutoSize(true);
}

// ============================
// DOWNLOAD
// ============================
$filename = 'Data Produk - ' . date('d M Y') . '.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;