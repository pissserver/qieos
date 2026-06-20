<?php include '../../sessions/session.php'; ?>

<!doctype html>
<html>
    <head>
        <title>Transfer ke Penjualan | Qieos</title>
        <?php include '../../script/headscript.php'; ?>

        <style>
            /* HERO HEADER */
            .transfer-header{
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

            .transfer-header h3{
                font-weight:800;
                margin:5px 0;
                color:#fff;
            }

            .transfer-header p{
                opacity:.85;
                margin:0;
            }

            .transfer-icon{
                width:90px;
                height:90px;
                border-radius:22px;
                background:rgba(255,255,255,.08);
                display:flex;
                justify-content:center;
                align-items:center;
                font-size:34px;
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

            /* REQUEST PANEL */

            .panel-warning{
                background:linear-gradient(
                    135deg,
                    #f59e0b,
                    #d97706
                );
                color:#fff;
            }

            .panel-warning .panel-icon{
                background:rgba(255,255,255,.15);
            }

            /* HISTORY PANEL */

            .panel-dark{
                background:linear-gradient(
                    135deg,
                    #334155,
                    #0f172a
                );
                color:#fff;
            }

            .panel-dark .panel-icon{
                background:rgba(255,255,255,.12);
            }

            /* EMPTY SEARCH */

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
        </style>
    </head>

    <body>
        <?php include '../components/sidebar.php'; ?>

        <main class="content">
            <?php include '../components/navbar.php'; ?>

            <div class="container-fluid px-0 mt-4">
                <!-- HEADER -->
                <!-- <div class="transfer-header mt-5">
                    <div>
                        <h3>Transfer ke Penjualan</h3>
                        <p class="mb-0">
                            Persetujuan request stok dari staff penjualan
                        </p>
                    </div>

                    <div class="transfer-icon">
                        <i class="fas fa-arrow-right-arrow-left"></i>
                    </div>
                </div> -->

                <!-- REQUEST PENDING -->
                <div class="section-card mb-4 mt-5">
                    <div class="panel-header panel-warning">
                        <div class="panel-left">
                            <div class="panel-icon">
                                <i class="fas fa-clock"></i>
                            </div>

                            <div>
                                <div class="panel-title">
                                    Request Pending
                                </div>
                                <div class="panel-subtitle">
                                    Menunggu approval transfer stok ke penjualan
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 px-4">
                        <div id="transfer-table"></div>
                    </div>
                </div>

                <!-- HISTORY -->
                <div class="section-card mb-5">
                    <div class="panel-header panel-dark">
                        <div class="panel-left">
                            <div class="panel-icon">
                                <i class="fas fa-history"></i>
                            </div>

                            <div>
                                <div class="panel-title">
                                    Riwayat Request
                                </div>
                                <div class="panel-subtitle">
                                    Histori seluruh permintaan stok gudang
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 px-4">
                        <div id="history-table"></div>
                    </div>
                </div>
            </div>
        </main>


        <?php include '../../script/footscript.php'; ?>

        <script>
            function loadTable(){
                fetch('stock-transfer-table.php')
                .then(res=>res.text())
                .then(html=>{
                    document.getElementById("transfer-table").innerHTML = html;
                })
                .catch(err=>{
                    console.error("Load table error:", err);
                });
            }

            function loadHistory(){
                fetch('../components/tables/history-request-table.php')
                .then(res=>res.text())
                .then(html=>{
                    document.getElementById("history-table").innerHTML = html;

                    setTimeout(() => {

                        // 🔥 DESTROY DULU
                        if ($.fn.DataTable.isDataTable('#requestHistory')) {
                            $('#requestHistory').DataTable().destroy();
                        }

                        // 🔥 INIT ULANG
                        $('#requestHistory').DataTable({
                            pageLength: 5,
                            lengthMenu:[[5,10,25,50],[5,10,25,50]],
                            responsive: true,
                            autoWidth: false,
                            language:{
                                search:"",
                                searchPlaceholder:"Cari request...",

                                zeroRecords: `
                                    <div class="empty-search">
                                        <img src="../../assets/img/illustrations/empty-data.png" class="empty-img">
                                        <div class="empty-title">Request tidak ditemukan</div>
                                        <div class="empty-sub">
                                            Coba gunakan kata kunci lain
                                        </div>
                                    </div>
                                `,

                                emptyTable: `
                                    <div class="empty-search">
                                        <img src="../../assets/img/illustrations/empty-data.png" class="empty-img">
                                        <div class="empty-title">Belum ada data request</div>
                                        <div class="empty-sub">
                                            Silakan tambahkan stok terlebih dahulu
                                        </div>
                                    </div>
                                `
                            },

                            // 🔥 PENTING: IKUTIN SORT SQL
                            order: [] 
                        });

                    }, 100);
                });
            }

            // First init
            loadTable();
            // Auto refresh setiap 1 detik
            setInterval(() => {
                loadTable();
            }, 3000);

            loadHistory();


            // 🔥 APPROVE
            function approve(id){
                Swal.fire({
                    title: 'Approve Request?',
                    text: "Stok akan dipindahkan dari gudang (FIFO)",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'ACC',
                    confirmButtonColor: '#16a34a'
                }).then((result)=>{
                    if(result.isConfirmed){

                        fetch('stock-transfer-action.php?action=approve',{
                            method:'POST',
                            headers:{'Content-Type':'application/x-www-form-urlencoded'},
                            body:'id='+id
                        })
                        .then(res=>res.json())
                        .then(res=>{
                            Swal.fire(res.status,res.msg,res.status);
                            loadTable();
                            loadHistory();
                        });

                    }
                });
            }

            // 🔥 REJECT
            function reject(id){
                Swal.fire({
                    title: 'Tolak Request?',
                    text:'Request tidak akan diproses',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Tolak',
                    confirmButtonColor:'#ef4444'
                }).then((result)=>{
                    if(result.isConfirmed){

                        fetch('stock-transfer-action.php?action=reject',{
                            method:'POST',
                            headers:{'Content-Type':'application/x-www-form-urlencoded'},
                            body:'id='+id
                        })
                        .then(res=>res.json())
                        .then(res=>{
                            console.log(res);
                            console.log(id);
                            Swal.fire(res.status,res.msg,res.status);
                            loadTable();
                            loadHistory();
                        });

                    }
                });
            }
        </script>
    </body>
</html>