<?php
include '../../sessions/session.php';

require '../../vendor/phpoffice/phpexcel/Classes/PHPExcel.php';
require '../../vendor/phpoffice/phpexcel/Classes/PHPExcel/IOFactory.php';

$q = mysqli_query($conn, "SELECT * FROM suppliers WHERE deleted_at IS NULL ORDER BY name ASC");

$objPHPExcel = new PHPExcel();
$objPHPExcel->getProperties()->setCreator('Qieos')->setTitle('Data Supplier');

$sheet = $objPHPExcel->setActiveSheetIndex(0);
$sheet->setTitle('Data Supplier');

// ============================
// HEADER LAPORAN
// ============================
$sheet->mergeCells('A1:E1');
$sheet->setCellValue('A1', 'PT. SELARASGRIYA SARANA UTAMA');
$sheet->mergeCells('A2:E2');
$sheet->setCellValue('A2', 'Pasar Induk Surabaya Sidotopo');
$sheet->mergeCells('A4:E4');
$sheet->setCellValue('A4', 'DATA SUPPLIER');
$sheet->mergeCells('A5:E5');
$sheet->setCellValue('A5', 'Per : ' . date('d M Y'));

$sheet->getStyle('A4')->applyFromArray(['font' => ['bold' => true, 'color' => ['rgb' => '1E1B4B']]]);
$sheet->getStyle('A5')->applyFromArray(['font' => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '64748B']]]);

// ============================
// TABLE HEADER (baris 7)
// ============================
$row = 7;
$headers = array('No', 'Nama Supplier', 'No. Telepon', 'Alamat', 'Catatan');
$col = 'A';
foreach ($headers as $h) {
    $sheet->setCellValue($col . $row, $h);
    $col++;
}

$sheet->getStyle("A7:E7")->applyFromArray([
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
    $sheet->setCellValue("B$row", $data['name']);
    $sheet->setCellValue("C$row", $data['phone'] ?: '-');
    $sheet->setCellValue("D$row", $data['address'] ?: '-');
    $sheet->setCellValue("E$row", $data['note'] ?: '-');
    $row++;
}

$lastDataRow = $row - 1;

// border semua data
$sheet->getStyle("A7:E" . $lastDataRow)->applyFromArray([
    'borders' => ['allborders' => ['style' => PHPExcel_Style_Border::BORDER_THIN]],
]);

// alignment
$sheet->getStyle("A7:E" . $lastDataRow)
    ->getAlignment()
    ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle("A7:A" . $lastDataRow)
    ->getAlignment()
    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle("C7:C" . $lastDataRow)
    ->getAlignment()
    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

// auto width
foreach (array('A', 'B', 'C', 'D', 'E') as $c) {
    $sheet->getColumnDimension($c)->setAutoSize(true);
}

// ============================
// DOWNLOAD
// ============================
$filename = 'Data Supplier - ' . date('d M Y') . '.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;