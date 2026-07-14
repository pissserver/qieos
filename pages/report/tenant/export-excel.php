<?php

include '../../../sessions/session.php';

require '../../../vendor/phpoffice/phpexcel/Classes/PHPExcel.php';
require '../../../vendor/phpoffice/phpexcel/Classes/PHPExcel/IOFactory.php';


$type       = isset($_GET['type']) ? $_GET['type'] : 'tenant';
$tab        = isset($_GET['tab']) ? $_GET['tab'] : 'all';

$tenant_id  = isset($_GET['tenant_id']) ? $_GET['tenant_id'] : '';

$first_date = isset($_GET['first_date']) ? $_GET['first_date'] : '';
$last_date  = isset($_GET['last_date']) ? $_GET['last_date'] : '';



$table = ($type == 'tenant')
    ? 'tenant_payments'
    : 'utility_payments';



$title = ($type == 'tenant')
    ? 'LAPORAN PEMBAYARAN TENANT'
    : 'LAPORAN PEMBAYARAN AIR & LISTRIK';


$title2 = ($type == 'tenant')
    ? 'Laporan Pembayaran Tenant'
    : 'Laporan Pembayaran Air & Listrik';



$where = [];



if(!empty($first_date) && !empty($last_date)){

    $where[] = "
        DATE(p.payment_date)
        BETWEEN '$first_date'
        AND '$last_date'
    ";

}



if($tab == "single" && !empty($tenant_id)){

    $where[] = "
        p.tenant_id='$tenant_id'
    ";

}



$whereSql = "";

if(count($where)>0){

    $whereSql = "WHERE ".implode(" AND ",$where);

}



$query = mysqli_query($conn,"
    SELECT
        p.*,
        t.tenant_name

    FROM $table p

    INNER JOIN tenants t
    ON t.id=p.tenant_id

    $whereSql

    ORDER BY p.payment_date DESC
");





// ============================
// CREATE EXCEL
// ============================

$objPHPExcel = new PHPExcel();


$objPHPExcel->getProperties()
->setCreator("Qieos")
->setTitle($title2);



$sheet = $objPHPExcel->setActiveSheetIndex(0);



$sheet->setTitle("Laporan");




// ============================
// HEADER LAPORAN
// ============================


$sheet->mergeCells('A1:E1');

$sheet->setCellValue(
    'A1',
    'PT. SELARASGRIYA SARANA UTAMA'
);


$sheet->mergeCells('A2:E2');

$sheet->setCellValue(
    'A2',
    'Pasar Induk Surabaya Sidotopo'
);



$sheet->mergeCells('A4:E4');

$sheet->setCellValue(
    'A4',
    $title
);



$periode = "";

if(!empty($first_date) && !empty($last_date)){

    $periode =
        "Periode : ".
        date('d M Y',strtotime($first_date)).
        " s/d ".
        date('d M Y',strtotime($last_date));

}


$sheet->mergeCells('A5:E5');

$sheet->setCellValue(
    'A5',
    $periode
);




// ============================
// TABLE HEADER
// ============================


$row = 7;


$headers = [
    "No",
    "Nama Tenant",
    "Tanggal",
    "Jumlah",
    "Status"
];


$col = 'A';


foreach($headers as $header){

    $sheet->setCellValue(
        $col.$row,
        $header
    );

    $col++;

}



// style header

$sheet->getStyle("A7:E7")
->applyFromArray([

    'font'=>[
        'bold'=>true,
        'color'=>[
            'rgb'=>'FFFFFF'
        ]
    ],

    'fill'=>[
        'type'=>PHPExcel_Style_Fill::FILL_SOLID,
        'color'=>[
            'rgb'=>'3F51B5'
        ]
    ],

    'alignment'=>[
        'horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER
    ],

    'borders'=>[
        'allborders'=>[
            'style'=>PHPExcel_Style_Border::BORDER_THIN
        ]
    ]

]);





// ============================
// DATA
// ============================


$row = 8;

$no = 1;

$total = 0;



while($data=mysqli_fetch_assoc($query)){


    $total += $data['cost_payment'];


    $sheet->setCellValue(
        "A$row",
        $no++
    );


    $sheet->setCellValue(
        "B$row",
        $data['tenant_name']
    );


    $sheet->setCellValue(
        "C$row",
        date(
            'd M Y',
            strtotime($data['payment_date'])
        )
    );


    $sheet->setCellValue(
        "D$row",
        $data['cost_payment']
    );


    $sheet->setCellValue(
        "E$row",
        ($data['status']=="paid")
        ? "Lunas"
        : ucfirst($data['status'])
    );



    $row++;

}




// ============================
// TOTAL
// ============================

$sheet->mergeCells("A$row:C$row");

$sheet->setCellValue(
    "A$row",
    "TOTAL PEMBAYARAN"
);

$firstDataRow = 8;
$lastDataRow  = $row - 1;

$sheet->setCellValue(
    "D$row",
    "=SUM(D$firstDataRow:D$lastDataRow)"
);


// style total
$sheet->getStyle("A$row:E$row")
->applyFromArray([

    'font'=>[
        'bold'=>true
    ],

    'fill'=>[
        'type'=>PHPExcel_Style_Fill::FILL_SOLID,
        'color'=>[
            'rgb'=>'F5F5F5'
        ]
    ],

    'borders'=>[
        'top'=>[
            'style'=>PHPExcel_Style_Border::BORDER_THIN
        ],
        'bottom'=>[
            'style'=>PHPExcel_Style_Border::BORDER_THIN
        ],
        'left'=>[
            'style'=>PHPExcel_Style_Border::BORDER_THIN
        ],
        'right'=>[
            'style'=>PHPExcel_Style_Border::BORDER_THIN
        ]
    ]

]);



// format rupiah

$sheet->getStyle("D8:D$row")
->getNumberFormat()
->setFormatCode(
    '"Rp " #,##0'
);




// border semua tabel

$sheet->getStyle("A7:E$row")
->applyFromArray([

    'borders'=>[

        'allborders'=>[
            'style'=>PHPExcel_Style_Border::BORDER_THIN
        ]

    ]

]);




// alignment

$sheet->getStyle("A7:E$row")
->getAlignment()
->setVertical(
    PHPExcel_Style_Alignment::VERTICAL_CENTER
);



$sheet->getStyle("A7:A$row")
->getAlignment()
->setHorizontal(
    PHPExcel_Style_Alignment::HORIZONTAL_CENTER
);


$sheet->getStyle("C7:C$row")
->getAlignment()
->setHorizontal(
    PHPExcel_Style_Alignment::HORIZONTAL_CENTER
);


$sheet->getStyle("D7:D$row")
->getAlignment()
->setHorizontal(
    PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
);





// auto width

foreach(range('A','E') as $col){

    $sheet
    ->getColumnDimension($col)
    ->setAutoSize(true);

}





// ============================
// DOWNLOAD
// ============================


$filename =
$title2.
" - ".
date('d M Y',strtotime($first_date)).
" s.d. ".
date('d M Y',strtotime($last_date)).
".xlsx";



header(
'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
);


header(
'Content-Disposition: attachment; filename="'.$filename.'"'
);


header('Cache-Control: max-age=0');



$objWriter = PHPExcel_IOFactory::createWriter(
    $objPHPExcel,
    'Excel2007'
);


$objWriter->save('php://output');

exit;