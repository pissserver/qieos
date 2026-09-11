<?php include '../../sessions/session.php'; ?>

<!doctype html>
<html>
    <head>
        <title>Transfer Gudang - Qieos</title>
        <?php include '../../script/headscript.php'; ?>

        <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/transfer.css">
        <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/transfer-table.css">
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
            var lastPendingHtml = '';
            var pendingLoading = false;

            function normalizeHtml(html){
                return (html || '').replace(/\s+/g, ' ').trim();
            }

            function canPollPending(){
                if (document.hidden) return false;
                if (document.querySelector('.q-confirm-overlay')) return false;
                return true;
            }

            function loadTable(force){
                if (pendingLoading) return;
                if (!force && !canPollPending()) return;

                pendingLoading = true;

                fetch('transfer-table.php', { cache: 'no-store' })
                .then(res=>res.text())
                .then(html=>{
                    var next = normalizeHtml(html);
                    if (!force && next === lastPendingHtml) return;

                    lastPendingHtml = next;
                    document.getElementById("transfer-table").innerHTML = html;
                })
                .catch(err=>{
                    console.error("Load table error:", err);
                })
                .then(function(){
                    pendingLoading = false;
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

            loadTable(true);
            loadHistory();

            setInterval(function(){
                loadTable();
            }, 4000);

            document.addEventListener('visibilitychange', function(){
                if (!document.hidden) loadTable();
            });


            // APPROVE
            function approve(id){

                QConfirm('Setujui Transfer?', 'Stok akan dipindahkan dari gudang ke penjualan.', {confirmText:'Setujui', icon:'fa-check', confirmClass:'q-confirm-btn-success', iconClass:'q-confirm-icon-success'}).then(function(ok){
                    if(ok){
                        fetch('transfer-action.php?action=approve',{
                            method:'POST',
                            headers:{'Content-Type':'application/x-www-form-urlencoded'},
                            body:'id='+id
                        })
                        .then(res=>res.json())
                        .then(res=>{
                            QToast(res.status,res.msg,res.status);
                            loadTable();
                            loadHistory();
                        });
                    }
                });

            }

            // REJECT
            function reject(id){

                QConfirm('Tolak Transfer?', 'Permintaan transfer ini akan ditolak.', {confirmText:'Tolak', icon:'fa-xmark', confirmClass:'q-confirm-btn-danger', iconClass:'q-confirm-icon-danger'}).then(function(ok){
                    if(ok){
                        fetch('transfer-action.php?action=reject',{
                            method:'POST',
                            headers:{'Content-Type':'application/x-www-form-urlencoded'},
                            body:'id='+id
                        })
                        .then(res=>res.json())
                        .then(res=>{
                            console.log(res);
                            console.log(id);
                            QToast(res.status,res.msg,res.status);
                            loadTable();
                            loadHistory();
                        });
                    }
                });

            }
        </script>
    </body>
</html>