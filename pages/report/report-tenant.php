<?php
    include '../../sessions/session.php';
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Laporan Tenant - Qieos</title>
    <?php include '../../script/headscript.php'; ?>

    <style>
        :root{
            --primary:#4f46e5;
            --dark:#0f172a;
            --muted:#64748b;
            --border:#e2e8f0;
        }

        /* HEADER */

        .stock-header{
            background:linear-gradient(135deg,#334155,#0f172a);
            color:#fff;
            border-radius:24px;
            padding:30px;
            display:flex;
            justify-content:space-between;
            align-items:center;
            margin-bottom:25px;
            box-shadow:0 12px 30px rgba(15,23,42,.15);
        }

        .stock-header h3{
            color:#fff;
            font-weight:800;
            margin-top:5px;
        }

        .stock-header p{
            margin:0;
            opacity:.85;
        }

        .header-icon{
            width:90px;
            height:90px;
            border-radius:22px;
            background:rgba(255,255,255,.08);
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:36px;
        }

        /* SECTION */

        .section-card{
            background:#fff;
            border-radius:24px;
            padding:24px;
            box-shadow:0 8px 24px rgba(15,23,42,.05);
        }

        /* PANEL */
            
        .panel-header{
            display:flex;
            justify-content:space-between;
            align-items:center;
            padding:18px 22px;
            border-radius:18px;
        }

        .panel-left{
            display:flex;
            align-items:center;
            gap:16px;
        }

        .panel-icon{
            width:58px;
            height:58px;
            border-radius:16px;
            background:rgba(255,255,255,.12);
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:22px;
        }

        .panel-title{
            font-size:17px;
            font-weight:700;
        }

        .panel-subtitle{
            font-size:13px;
            opacity:.85;
        }

        .panel-primary{
            background:linear-gradient(
                135deg,
                #4f46e5,
                #4338ca
            );
            color:#fff;
        }

        /* DATATABLE */

        .dataTables_filter input{
            border-radius:8px !important;
            border:1px solid #cbd5e1 !important;
            padding:8px 14px !important;
            width:180px !important;
        }

        .dataTables_length select{
            min-width:75px !important;
            height:38px !important;
            padding:0 30px 0 12px !important;
            border-radius:8px !important;
            border:1px solid #cbd5e1 !important;
        }

        /* TABLE */

        #stockTable{
            border-collapse:separate;
            border-spacing:0 14px;
        }

        #stockTable thead th{
            border:none !important;
            color:#64748b;
            font-size:12px;
            text-transform:uppercase;
        }

        .stock-row{
            background:#fff;
            box-shadow:0 8px 18px rgba(15,23,42,.05);
            transition:.25s;
            border-radius:14px;
            cursor:pointer;
        }

        .stock-row:hover{
            transform:translateY(-2px);
            box-shadow:0 14px 28px rgba(15,23,42,.10);
        }

        .stock-row td:first-child{
            border-radius:14px 0 0 14px;
        }

        .stock-row td:last-child{
            border-radius:0 14px 14px 0;
        }

        #stockTable tbody td{
            border:none !important;
            padding:18px;
            vertical-align:middle;
        }

        .product-wrap{
            display:flex;
            align-items:center;
            gap:15px;
        }

        .product-icon{
            width:50px;
            height:50px;
            border-radius:14px;
            background:linear-gradient(135deg,#334155,#0f172a);
            color:#fff;
            display:flex;
            align-items:center;
            justify-content:center;
        }

        .stock-badge{
            padding:8px 14px;
            border-radius:10px;
            font-weight:700;
        }

        .stock-success{
            background:#dcfce7;
            color:#166534;
        }

        .stock-danger{
            background:#fee2e2;
            color:#991b1b;
        }

        .stock-empty{
            background:#e2e8f0;
            color:#475569;
        }

        .unit-badge{
            padding:8px 14px;
            border-radius:10px;
            background:#f8fafc;
            border:1px solid #e2e8f0;
            font-weight:600;
        }

        .table-card{
            background:#fff;
            border-radius:20px;
            padding:20px;
            box-shadow:0 8px 20px rgba(15,23,42,.05);
        }

        /* ACTION */
        .action-btn{
            width:38px;
            height:38px;
            border:none;
            border-radius:10px;
            margin:0 4px;
            color:#fff;
            transition:.25s;
        }

        .btn-edit{
            background:#f59e0b;
        }

        .empty-search{
            padding:50px 20px;
            text-align:center;
        }

        .empty-img{
            width:370px;
            opacity:.9;
            margin-bottom:18px;
        }

        .empty-title{
            font-size:18px;
            font-weight:700;
            color:#0f172a;
            margin-bottom:6px;
        }

        .empty-sub{
            font-size:14px;
            color:#64748b;
        }

        /* FIFO CONTAINER */

        .detail-container{
            background:#fff;
            border-radius:20px;
            padding:25px;
            box-shadow:0 8px 20px rgba(15,23,42,.05);
        }

        .fifo-title{
            display:flex;
            align-items:center;
            gap:10px;
            margin-bottom:15px;
        }

        /* Modal Edit */
        /* PANEL */
            
        .panel-header{
            display:flex;
            justify-content:space-between;
            align-items:center;
            padding:18px 22px;
            border-radius:18px;
        }

        .panel-left{
            display:flex;
            align-items:center;
            gap:16px;
        }

        .panel-icon{
            width:58px;
            height:58px;
            border-radius:16px;
            background:rgba(255,255,255,.12);
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:22px;
        }

        .panel-title{
            font-size:17px;
            font-weight:700;
        }

        .panel-subtitle{
            font-size:13px;
            opacity:.85;
        }

        .panel-primary{
            background:linear-gradient(
                135deg,
                #4f46e5,
                #4338ca
            );
            color:#fff;
        }


        .panel-dark{
            background:linear-gradient(
                135deg,
                #334155,
                #0f172a
            );
            color:#fff;
        }

        .panel-toggle-wrap{
            display:flex;
            align-items:center;
            gap:10px;
        }

        .toggle-label{
            font-size:13px;
            color:#cbd5e1;
            font-weight:500;
            transition:.25s;
        }

        @keyframes fadeSlide{
            from{
                opacity:0;
                transform:translateY(8px);
            }
            to{
                opacity:1;
                transform:translateY(0);
            }
        }

        /* SECTION */
        .section-title{
            display:inline-block;
            background:#edf4ff;
            color:var(--primary-dark);
            padding:5px 12px;
            border-radius:7px;
            font-size:12px;
            font-weight:600;
            margin-bottom:14px;
        }

        /* FORM */
        .input-group-modern{
            display:flex;
            align-items:center;
            margin-bottom:14px;
        }

        .input-icon{
            width:42px;
            height:42px;
            border-radius:10px;
            background:linear-gradient(135deg,#334155,#1e293b);
            display:flex;
            align-items:center;
            justify-content:center;
            color:#fff;
            margin-right:10px;
            box-shadow:0 4px 10px rgba(15,23,42,.18);
        }

        .form-control{
            height:42px;
            border-radius:8px;
            border:1px solid var(--border);
            font-size:14px;
        }

        textarea.form-control{
            height:65px;
        }

        .form-control:focus{
            border-color:var(--primary);
            box-shadow:0 0 0 3px rgba(63,130,196,.15);
        }

        /* BUTTON */
        .btn-save{
            background:linear-gradient(90deg,#1e293b,#334155);
            border:none;
            border-radius:8px;
            color:#fff;
            padding:10px 24px;
            font-weight:600;
        }

        .modal-backdrop.show{
            opacity:.55 !important;
        }

        .payment-tabs{
            display:flex;
            gap:12px;
            background:#f4f6fb;
            padding:8px;
            border-radius:18px;
            margin:20px 0 25px;
        }

        .payment-tab{
            flex:1;
            border:none;
            background:transparent;
            padding:14px 20px;
            border-radius:14px;
            font-weight:700;
            color:#64748b;
            transition:.3s;
            cursor:pointer;
        }

        .payment-tab i{
            margin-right:8px;
        }

        .payment-tab:hover{
            background:#fff;
            color:#4338ca;
        }

        .payment-tab.active{

            background:linear-gradient(
                135deg,
                #5b4cf4,
                #4038d7
            );
            color:#fff;
            box-shadow:
                0 10px 25px rgba(91,76,244,.25);
        }

        .loading-box{
            height:280px;
            display:flex;
            align-items:center;
            justify-content:center;
            flex-direction:column;
            color:#94a3b8;
            font-size:15px;
        }

        .loading-box i{
            font-size:28px;
            margin-bottom:15px;
        }

        /* ===========================
        REPORT MENU
        ===========================*/

        .report-menu{
            display:flex;
            flex-direction:column;
            gap:14px;
        }

        .report-menu-item{
            width:100%;
            border:none;
            background:#fff;
            border:1px solid #e8edf7;
            border-radius:18px;
            padding:18px;
            display:flex;
            align-items:center;
            gap:18px;
            text-align:left;
            transition:.35s;
            cursor:pointer;
            box-shadow:0 5px 18px rgba(15,23,42,.04);
        }

        .report-menu-item:hover{
            transform:translateY(-3px);
            border-color:#4f46e5;
            box-shadow:0 14px 35px rgba(79,70,229,.15);
        }

        .report-menu-item i{
            width:56px;
            height:56px;
            border-radius:16px;
            background:linear-gradient(135deg,#4f46e5,#4338ca);
            color:#fff;
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:22px;
            flex-shrink:0;
        }

        .report-menu-item h6{
            margin:0;
            font-size:15px;
            font-weight:700;
            color:#0f172a;
        }

        .report-menu-item small{
            color:#64748b;
            display:block;
            margin-top:4px;
        }

        .report-menu-item.active{

            background:
                linear-gradient(
                    135deg,
                    #5b4cf4,
                    #4338ca
                );

            color:#fff;

            border:none;

            box-shadow:
                0 18px 40px rgba(91,76,244,.28);

        }

        .report-menu-item.active h6,
        .report-menu-item.active small{
            color:#fff;
        }

        .report-menu-item.active i{

            background:
                rgba(255,255,255,.18);

            backdrop-filter:blur(12px);

        }

        /* ===========================
        FILTER CARD
        ===========================*/

        #allTenantContent{

            animation:fadeReport .35s;

        }

        .filter-card{

            background:#f8faff;

            border:1px solid #edf2fb;

            border-radius:18px;

            padding:22px;

            margin-bottom:25px;

        }

        .form-label{

            font-weight:700;

            color:#334155;

            margin-bottom:8px;

        }

        .form-control{

            height:48px;

            border-radius:12px;

            border:1px solid #dbe4f0;

            transition:.3s;

        }

        .form-control:focus{

            border-color:#5b4cf4;

            box-shadow:
                0 0 0 .2rem rgba(91,76,244,.12);

        }

        /* ===========================
        BUTTON
        ===========================*/

        .btn-success,
        .btn-danger{

            height:48px;

            border:none;

            border-radius:12px;

            font-weight:600;

            transition:.3s;

        }

        .btn-success{

            background:
                linear-gradient(
                    135deg,
                    #22c55e,
                    #16a34a
                );

        }

        .btn-danger{

            background:
                linear-gradient(
                    135deg,
                    #ef4444,
                    #dc2626
                );

        }

        .btn-success:hover,
        .btn-danger:hover{

            transform:translateY(-2px);

            box-shadow:
                0 10px 25px rgba(15,23,42,.15);

        }

        /* ===========================
        TABLE
        ===========================*/

        #reportTable{

            border-collapse:separate;

            border-spacing:0 14px;

        }

        #reportTable thead th{

            border:none !important;

            background:#f8faff;

            color:#64748b;

            text-transform:uppercase;

            font-size:12px;

            font-weight:700;

            padding:16px;

        }

        #reportTable tbody tr{

            background:#fff;

            transition:.25s;

            box-shadow:
                0 8px 20px rgba(15,23,42,.05);

        }

        #reportTable tbody tr:hover{

            transform:translateY(-2px);

            box-shadow:
                0 15px 35px rgba(15,23,42,.09);

        }

        #reportTable tbody td{

            border:none !important;

            padding:18px;

            vertical-align:middle;

        }

        #reportTable tbody td:first-child{

            border-radius:14px 0 0 14px;

        }

        #reportTable tbody td:last-child{

            border-radius:0 14px 14px 0;

        }

        /* ===========================
        STATUS BADGE
        ===========================*/

        .status-paid{

            display:inline-block;

            padding:7px 14px;

            border-radius:30px;

            background:#dcfce7;

            color:#15803d;

            font-size:12px;

            font-weight:700;

        }

        .status-unpaid{

            display:inline-block;

            padding:7px 14px;

            border-radius:30px;

            background:#fee2e2;

            color:#b91c1c;

            font-size:12px;

            font-weight:700;

        }

        .status-partial{

            display:inline-block;

            padding:7px 14px;

            border-radius:30px;

            background:#fef3c7;

            color:#b45309;

            font-size:12px;

            font-weight:700;

        }

        /* ===========================
        EMPTY
        ===========================*/

        .loading-box{

            padding:70px 20px;

            display:flex;

            flex-direction:column;

            align-items:center;

            justify-content:center;

            color:#94a3b8;

        }

        .loading-box i{

            font-size:55px;

            margin-bottom:18px;

            color:#5b4cf4;

        }

        .loading-box h5{

            font-weight:700;

            color:#334155;

            margin-bottom:8px;

        }

        .loading-box span{

            max-width:420px;

            text-align:center;

            line-height:1.7;

        }

        /* ===========================
        ANIMATION
        ===========================*/

        @keyframes fadeReport{

            from{

                opacity:0;

                transform:translateY(18px);

            }

            to{

                opacity:1;

                transform:translateY(0);

            }

        }

        /* ===========================
        RESPONSIVE
        ===========================*/

        @media(max-width:991px){

            .report-menu{

                flex-direction:row;

                overflow:auto;

                margin-bottom:20px;

            }

            .report-menu-item{

                min-width:240px;

            }

        }

        @media(max-width:768px){

            .btn-success,
            .btn-danger{

                margin-top:10px;

            }

            .panel-header{

                flex-direction:column;

                align-items:flex-start;

                gap:15px;

            }

        }
    </style>
</head>

<body>
<?php include '../components/sidebar.php'; ?>
<main class="content">
    <?php include '../components/navbar.php'; ?>

    <div class="container-fluid px-0 mt-5">
        <div class="row">
            <div class="col-md-12">
                <div class="detail-container mb-5">

                    <div class="payment-tabs">

                        <button
                            class="payment-tab active"
                            onclick="switchMainTab('tenant', this)">
                            <i class="fas fa-store"></i>
                            Pembayaran Tenant
                        </button>

                        <button
                            class="payment-tab"
                            onclick="switchMainTab('utility', this)">
                            <i class="fas fa-bolt"></i>
                            Air & Listrik
                        </button>

                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div id="mainContent" class="row mt-n2 mb-5">

            <!-- Sidebar -->
            <div class="col-lg-3">

                <div class="section-card">

                    <div class="panel-header panel-dark mb-4">
                        <div class="panel-left">
                            <div class="panel-icon">
                                <i class="fas fa-folder-open"></i>
                            </div>

                            <div>
                                <div class="panel-title">
                                    Jenis Laporan
                                </div>
                                <div class="panel-subtitle">
                                    Pilih laporan yang ingin ditampilkan
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="report-menu">

                        <button class="report-menu-item active"
                            onclick="switchReportTab('all', this)">
                            <i class="fas fa-layer-group"></i>

                            <div>
                                <h6>Semua Tenant</h6>
                                <small>Seluruh pembayaran tenant</small>
                            </div>
                        </button>

                        <button class="report-menu-item"
                            onclick="switchReportTab('tenant', this)">
                            <i class="fas fa-store"></i>

                            <div>
                                <h6>Per Tenant</h6>
                                <small>Laporan berdasarkan tenant</small>
                            </div>
                        </button>

                    </div>

                </div>

            </div>


            <!-- Content -->
            <div class="col-lg-9">
                <input type="hidden" id="reportType" value="tenant">

                <?php include 'tenant/content-all.php' ?>
                <?php include 'tenant/content-single.php' ?>
            </div>

        </div>
    </div>
</main>

<?php include '../../script/footscript.php'; ?>

<!-- Switch Main Tab -->
<script>
    let currentMainTab = "tenant";
    let currentSubTab  = "all";

    function switchMainTab(type, el){

        $(".payment-tab").removeClass("active");
        $(el).addClass("active");

        currentMainTab = type;

        $("#reportType").val(type);

        if(type == "tenant"){

            $("#titleReport").text("Laporan Pembayaran Tenant");
            $("#subtitleReport").text("Menampilkan seluruh riwayat pembayaran seluruh tenant.");

        }else{

            $("#titleReport").text("Laporan Pembayaran Air & Listrik");
            $("#subtitleReport").text("Menampilkan seluruh riwayat pembayaran air & listrik.");

        }

        if(currentSubTab == "all"){
            loadReportAll();
        }else{
            loadReportSingle();
        }

    }
</script>

<!-- Switch Report Tab -->
<script>
    function switchReportTab(tab, el){

        currentSubTab = tab;

        $(".report-menu-item").removeClass("active");
        $(el).addClass("active");

        $("#allTenantContent").hide();
        $("#tenantContent").hide();

        if(tab == "all"){

            $("#allTenantContent").show();

            loadReportAll();

        }else{

            $("#tenantContent").show();

            loadReportSingle();

        }

    }
</script>

<!-- Load Data Report -->
<script>
    function loadReportAll(){

        let first = $("#first_date_all").val();
        let last  = $("#last_date_all").val();

        $.get(
            "tenant/report-all.php",
            {
                type: $("#reportType").val(),
                first_date: first,
                last_date: last
            },
            function(res){

                $("#reportAllBody").html(res);

            }
        );

    }

    $("#first_date_all,#last_date_all").on("change",function(){

        loadReportAll();

    });

    function loadReportSingle(){

        let id    = $("#tenant_id").val();
        let first = $("#first_date_single").val();
        let last  = $("#last_date_single").val();

        $.get(
            "tenant/report-single.php",
            {
                type: $("#reportType").val(),
                tenant_id: id,
                first_date: first,
                last_date: last
            },
            function(res){

                $("#reportSingleBody").html(res);

            }
        );

    }

    $("#tenant_id,#first_date_single,#last_date_single").on("change",function(){

        loadReportSingle();

    });
</script>

<!-- Print & Export -->
<script>
    function printPDF(tab){

        let type = $("#reportType").val();
        let url = "tenant/print-pdf.php?type=" + type;

        if(tab == "all"){

            url += "&tab=all";
            url += "&first_date=" + $("#first_date_all").val();
            url += "&last_date=" + $("#last_date_all").val();

        }else{

            url += "&tab=single";
            url += "&tenant_id=" + $("#tenant_id").val();
            url += "&first_date=" + $("#first_date_single").val();
            url += "&last_date=" + $("#last_date_single").val();

        }

        window.open(url, "_blank");

    }
</script>

</body>
</html>