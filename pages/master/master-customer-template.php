<?php
include '../../sessions/session.php';

require '../../vendor/phpoffice/phpexcel/Classes/PHPExcel.php';
require '../../vendor/phpoffice/phpexcel/Classes/PHPExcel/IOFactory.php';

$objPHPExcel = new PHPExcel();
$objPHPExcel->getProperties()->setCreator('Qieos')->setTitle('Template Import Customer');

$sheet = $objPHPExcel->setActiveSheetIndex(0);
$sheet->setTitle('Import Customer');

$sheet->getColumnDimension('A')->setWidth(34);
$sheet->getColumnDimension('B')->setWidth(22);

// Judul
$sheet->mergeCells('A1:B1');
$sheet->setCellValue('A1', 'TEMPLATE IMPORT CUSTOMER');
$sheet->getStyle('A1')->applyFromArray([
    'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '1E1B4B']],
]);

// Instruksi
$sheet->mergeCells('A2:B2');
$sheet->setCellValue('A2', 'Isi data mulai baris 5. JANGAN mengubah / menghapus baris header (baris 4).');
$sheet->mergeCells('A3:B3');
$sheet->setCellValue('A3', 'Kolom "name" (Nama) wajib diisi, "phone" (No. Telepon) boleh kosong. Baris yang diawali tanda # otomatis dilewati saat import.');
foreach (array('A2', 'A3') as $c) {
    $sheet->getStyle($c)->applyFromArray([
        'font' => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '64748B']],
    ]);
}

// Header kolom (baris 4)
$headers = ['name', 'phone'];
$col = 'A';
foreach ($headers as $h) {
    $sheet->setCellValue($col . '4', $h);
    $col++;
}
$sheet->getStyle('A4:B4')->applyFromArray([
    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
    'fill' => ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => ['rgb' => '4F46E5']],
    'alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER],
    'borders' => ['allborders' => ['style' => PHPExcel_Style_Border::BORDER_THIN]],
]);

// Baris contoh (diawali '#', dilewati import tanpa perlu dihapus)
$sheet->setCellValue('A5', '#contoh: Budi Santoso');
$sheet->setCellValue('B5', '081234567890');
$sheet->getStyle('A5:B5')->applyFromArray([
    'font' => ['italic' => true, 'color' => ['rgb' => '94A3B8']],
    'borders' => ['allborders' => ['style' => PHPExcel_Style_Border::BORDER_DOTTED]],
]);

// Grid kosong siap isi
for ($r = 6; $r <= 45; $r++) {
    $sheet->getStyle("A$r:B$r")->applyFromArray([
        'borders' => [
            'allborders' => [
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => ['rgb' => 'E2E8F0'],
            ],
        ],
    ]);
}

$sheet->getStyle('A4:B45')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="Template Import Customer.xlsx"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;