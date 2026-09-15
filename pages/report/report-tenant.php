<?php
    include '../../sessions/session.php';
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Laporan Tenant - Qieos</title>
    <?php include '../../script/headscript.php'; ?>

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/report-tenant.css?v=<?= filemtime(__DIR__ . '/../../css/pages/report-tenant.css') ?>">
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

                    <div class="panel-header panel-primary mb-4">
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


        // jika tanggal belum dipilih
        if(first == "" || last == ""){

            $("#reportAllSummaryCard").hide();
            $("#reportAllBody").html(`

                <tr>

                    <td colspan="5">

                        <div class="loading-box">

                            <i class="fas fa-file-invoice-dollar"></i>

                            <h5>Belum Ada Data</h5>

                            <span>
                                Silakan pilih tanggal awal dan tanggal akhir
                                untuk menampilkan laporan.
                            </span>

                        </div>

                    </td>

                </tr>

            `);

            return;

        }


        $.get(
            "tenant/report-all.php",
            {
                type: $("#reportType").val(),
                first_date: first,
                last_date: last
            },
            function(res){
                let parts = res.split("<!--SPLIT_FOOT-->");
                $("#reportAllBody").html(parts[0]);

                let footText = parts[1] || "";
                let match = footText.match(/Rp\s*[\d\.\,]+/i);
                if (match) {
                    $("#totalAllAmountLabel").text(match[0]);
                    $("#reportAllSummaryCard").show();
                } else {
                    $("#reportAllSummaryCard").hide();
                }
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


        // belum pilih tenant
        if(id == ""){

            $("#reportSingleSummaryCard").hide();
            $("#reportSingleBody").html(`

                <tr>

                    <td colspan="4">

                        <div class="loading-box">

                            <i class="fas fa-store"></i>

                            <h5>Belum Ada Data</h5>

                            <span>
                                Pilih tenant terlebih dahulu untuk melihat laporan pembayaran.
                            </span>

                        </div>

                    </td>

                </tr>

            `);

            return;

        }


        // tenant sudah dipilih tapi tanggal belum lengkap
        if(first == "" || last == ""){

            $("#reportSingleSummaryCard").hide();
            $("#reportSingleBody").html(`

                <tr>

                    <td colspan="4">

                        <div class="loading-box">

                            <i class="fas fa-calendar-alt"></i>

                            <h5>Pilih Periode</h5>

                            <span>
                                Silakan pilih tanggal awal dan tanggal akhir
                                untuk menampilkan laporan.
                            </span>

                        </div>

                    </td>

                </tr>

            `);

            return;

        }


        $.get(
            "tenant/report-single.php",
            {
                type: $("#reportType").val(),
                tenant_id: id,
                first_date: first,
                last_date: last
            },
            function(res){
                let parts = res.split("<!--SPLIT_FOOT-->");
                $("#reportSingleBody").html(parts[0]);

                let footText = parts[1] || "";
                let match = footText.match(/Rp\s*[\d\.\,]+/i);
                if (match) {
                    $("#totalSingleAmountLabel").text(match[0]);
                    $("#reportSingleSummaryCard").show();
                } else {
                    $("#reportSingleSummaryCard").hide();
                }
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

    function printExcel(tab){

        let type = $("#reportType").val();
        let url = "tenant/export-excel.php?type=" + type;

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

        window.open(url, "_self");

    }

    $(function(){
        const now = new Date();
        const y = now.getFullYear();
        const m = String(now.getMonth() + 1).padStart(2, "0");
        const d = String(now.getDate()).padStart(2, "0");
        const firstDay = y + "-" + m + "-01";
        const today = y + "-" + m + "-" + d;

        $("#first_date_all, #first_date_single").each(function(){
            if (!$(this).val()) $(this).val(firstDay);
        });

        $("#last_date_all, #last_date_single").each(function(){
            if (!$(this).val()) $(this).val(today);
        });

        loadReportAll();
    });
</script>

</body>
</html>