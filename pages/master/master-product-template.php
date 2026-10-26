<?php
include '../../sessions/session.php';

require '../../vendor/phpoffice/phpexcel/Classes/PHPExcel.php';
require '../../vendor/phpoffice/phpexcel/Classes/PHPExcel/IOFactory.php';

// Semua kategori yang boleh diisi via import (kategori "Additional" dibuat manual
// lewat form, jadi sengaja tidak muncul di sini). Pakai daftar yang sama untuk
// dropdown Excel & validasi di master-product-import.php.
$categories = ['Makanan', 'Minuman', 'Jajanan', 'Pelengkap'];

// Unit aktif dari tabel units (untuk dropdown Excel kolom unit).
$units = array();
$qu = mysqli_query($conn, "SELECT name FROM units WHERE deleted_at IS NULL ORDER BY name ASC");
if ($qu) {
    while ($d = mysqli_fetch_assoc($qu)) {
        $units[] = $d['name'];
    }
}

$objPHPExcel = new PHPExcel();
$objPHPExcel->getProperties()->setCreator('Qieos')->setTitle('Template Import Produk');

$sheet = $objPHPExcel->setActiveSheetIndex(0);
$sheet->setTitle('Import Produk');

$sheet->getColumnDimension('A')->setWidth(20);
$sheet->getColumnDimension('B')->setWidth(40);
$sheet->getColumnDimension('C')->setWidth(18);
$sheet->getColumnDimension('D')->setWidth(16);
$sheet->getColumnDimension('E')->setWidth(16);
$sheet->getColumnDimension('F')->setWidth(16);
$sheet->getColumnDimension('G')->setWidth(18);

// Judul
$sheet->mergeCells('A1:G1');
$sheet->setCellValue('A1', 'TEMPLATE IMPORT PRODUK');
$sheet->getStyle('A1')->applyFromArray([
    'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '1E1B4B']],
]);

// Instruksi
$sheet->mergeCells('A2:G2');
$sheet->setCellValue('A2', 'Isi data mulai baris 5. JANGAN mengubah / menghapus baris header (baris 4).');
$sheet->mergeCells('A3:G3');
$sheet->setCellValue('A3', 'Kolom "code", "name", "category", "sell_price" wajib diisi; "unit" pilih dari dropdown (boleh kosong); "low_stock" (batas menipis gudang) & "low_stock_kantin" (batas menipis kantin) isi angka (boleh 0). Baris yang diawali tanda # otomatis dilewati saat import.');
foreach (array('A2', 'A3') as $c) {
    $sheet->getStyle($c)->applyFromArray([
        'font' => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '64748B']],
    ]);
}

// Header kolom (baris 4)
$headers = ['code', 'name', 'category', 'sell_price', 'unit', 'low_stock', 'low_stock_kantin'];
$col = 'A';
foreach ($headers as $h) {
    $sheet->setCellValue($col . '4', $h);
    $col++;
}
$sheet->getStyle('A4:G4')->applyFromArray([
    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
    'fill' => ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => ['rgb' => '4F46E5']],
    'alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER],
    'borders' => ['allborders' => ['style' => PHPExcel_Style_Border::BORDER_THIN]],
]);

// Baris contoh (diawali '#', dilewati import tanpa perlu dihapus)
$sheet->setCellValue('A5', '#contoh: MKN-001');
$sheet->setCellValue('B5', 'Keripik Kentang 100g');
$sheet->setCellValue('C5', 'Makanan');
$sheet->setCellValue('D5', 12000);
$sheet->setCellValue('E5', 'pcs');
$sheet->setCellValue('F5', 10);
$sheet->setCellValue('G5', 5);
$sheet->getStyle('A5:G5')->applyFromArray([
    'font' => ['italic' => true, 'color' => ['rgb' => '94A3B8']],
    'borders' => ['allborders' => ['style' => PHPExcel_Style_Border::BORDER_DOTTED]],
]);

// Grid kosong siap isi
for ($r = 6; $r <= 45; $r++) {
    $sheet->getStyle("A$r:G$r")->applyFromArray([
        'borders' => [
            'allborders' => [
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => ['rgb' => 'E2E8F0'],
            ],
        ],
    ]);
}

// ---- Data validation kategori: dropdown pilihan dari daftar kategori ----
// Catatan: getShowDropDown() sengaja = true karena PHPExcel menulis atribut
// showDropDown terbalik dibanding nilainya — writer meng-output "0" ketika
// true, dan di format xlsx atribut itulah yang membuat panah dropdown tampil.
$objValidation = new PHPExcel_Cell_DataValidation();
$objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
$objValidation->setFormula1('"' . implode(',', $categories) . '"');
$objValidation->setAllowBlank(true);
$objValidation->setShowDropDown(true);
$objValidation->setShowInputMessage(true);
$objValidation->setShowErrorMessage(true);
$objValidation->setPromptTitle('Pilih Kategori');
$objValidation->setPrompt('Pilih salah satu kategori dari daftar.');
$objValidation->setErrorTitle('Kategori tidak valid');
$objValidation->setError('Kategori harus salah satu dari: ' . implode(', ', $categories));
// Baris 5 adalah baris contoh sekaligus awal isi data — ikut diberi dropdown.
$sheet->setDataValidation('C5:C45', $objValidation);

// ---- Data validation unit: dropdown dari tabel units ----
$unitsList = array_values(array_unique(array_map('trim', $units)));
if (!empty($unitsList)) {
    // Formula list Excel max 255 karakter; kalau unit banyak, pindah ke sheet tersembunyi.
    $formula = '"' . implode(',', $unitsList) . '"';
    if (strlen($formula) > 255) {
        $hidden = $objPHPExcel->createSheet();
        $hidden->setTitle('Units');
        $hidden->setSheetState(PHPExcel_Worksheet::SHEETSTATE_HIDDEN);
        $hr = 1;
        foreach ($unitsList as $u) {
            $hidden->setCellValue('A' . $hr, $u);
            $hr++;
        }
        $formula = 'Units!$A$1:$A$' . ($hr - 1);
    }

    $objValUnit = new PHPExcel_Cell_DataValidation();
    $objValUnit->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
    $objValUnit->setFormula1($formula);
    $objValUnit->setAllowBlank(true);
    $objValUnit->setShowDropDown(true);
    $objValUnit->setShowInputMessage(true);
    $objValUnit->setShowErrorMessage(true);
    $objValUnit->setPromptTitle('Pilih Unit');
    $objValUnit->setPrompt('Pilih salah satu unit dari daftar.');
    $objValUnit->setErrorTitle('Unit tidak valid');
    $objValUnit->setError('Unit harus salah satu dari: ' . implode(', ', $unitsList));
    $sheet->setDataValidation('E5:E45', $objValUnit);
}

$sheet->getStyle('A4:G45')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('D4:D45')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$sheet->getStyle('F4:G45')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="Template Import Produk.xlsx"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;