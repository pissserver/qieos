<?php include '../../sessions/session.php'; ?>

<!doctype html>
<html>
    <head>
        <title>Transfer Gudang - Qieos</title>
        <?php include '../../script/headscript.php'; ?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/history-request-table.css?v=<?php echo filemtime(__DIR__ . '/../../css/pages/history-request-table.css'); ?>">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/transfer.css?v=<?php echo filemtime(__DIR__ . '/../../css/pages/transfer.css'); ?>">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/transfer-table.css?v=<?php echo filemtime(__DIR__ . '/../../css/pages/transfer-table.css'); ?>">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/transfer-history.css?v=<?php echo filemtime(__DIR__ . '/../../css/pages/transfer-history.css'); ?>">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/components/datatable-premium.css?v=<?php echo filemtime(__DIR__ . '/../../css/components/datatable-premium.css'); ?>">
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
                <div class="section-card transfer-pending-card mb-4 mt-5">
                    <div class="panel-header panel-primary">
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
                <div class="section-card transfer-history-card mb-5">
                    <div class="panel-header panel-primary">
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
                        <div id="history-table" class="dt-premium"></div>
                    </div>
                </div>
            </div>
        </main>

        <?php include '../components/modals/request-detail-modal.php'; ?>

        <?php include '../../script/footscript.php'; ?>

        <script src="<?php echo BASE_URL; ?>/script/datatable-compact.js?v=<?php echo filemtime(__DIR__ . '/../../script/datatable-compact.js'); ?>"></script>

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

            /* ---------- DataTable riwayat request ---------- */

            /* Breakpoint & pager milik helper bersama
               script/datatable-compact.js, sama seperti Master, Purchasing,
               Stok Gudang, dan Mutasi. */
            function isTransferMobile(){
                return MP_TABLE.isMobile();
            }

            let transferIsMobile = isTransferMobile();

            function historyDataTableOptions(){
                const mobile = isTransferMobile();

                const options = {
                    pageLength: 5,
                    lengthMenu: [[5,10,25,50],[5,10,25,50]],

                    /* Pager kustom dari script/datatable-compact.js: 3 nomor
                       di mobile, 5 di tablet/desktop, halaman 1 & terakhir
                       dikunci di dua ujung, dan "…" hanya muncul kalau memang
                       ada nomor yang disembunyikan. Bentuk pil & warnanya sudah
                       diatur di css/components/datatable-premium.css.

                       DULU halaman ini memakai pager bawaan DataTables
                       ("simple_numbers" asumsi default) yang menambah pil
                       "Previous"/"Next" berupa teks. */
                    pagingType: "mp_compact",

                    /* Chip "Menampilkan 1-5 dari 87 request", plus
                       "dari N request" kalau sedang difilter. */
                    infoCallback: MP_TABLE.infoCallback('request'),

                    autoWidth: false,

                    /* "dom" HANYA di-set untuk mobile. Di desktop/tablet TIDAK
                       di-set, sama seperti halaman lain, jadi wrapper .row +
                       .col-* bawaan Bootstrap 5 yang dipakai
                       css/components/datatable-premium.css.

                       DULU "dom" tidak pernah di-set sama sekali, termasuk di
                       mobile, sehingga Show n entries & baris info ikut
                       disembunyikan oleh rule
                       ".dataTables_wrapper .dataTables_length/_info{display:none}"
                       di css/pages/transfer-history.css. Rule itu sudah
                       dihapus, jadi mobile sekarang pakai "fltip": search +
                       show entries + tabel + chip info + pagination. */
                    language:{
                        search:"",
                        searchPlaceholder:"Cari request...",

                        paginate:{
                            previous:"&#8249;",  // ‹
                            next:"&#8250;"       // ›
                        },

                        oAria:{
                            paginate:{
                                pageLabel:"Halaman {page}",
                                previous:"Halaman sebelumnya",
                                next:"Halaman berikutnya"
                            }
                        },

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

                    /* PENTING: IKUTIN SORT SQL */
                    order: []
                };

                if(mobile) options.dom = "fltip";

                return options;
            }

            function historyDataTable(){
                const table = $('#requestHistory');
                if(!table.length) return null;

                const ht = table.DataTable(historyDataTableOptions());
                ht.columns.adjust();

                return ht;
            }

            function loadHistory(){
                /* --- Ambil state SEBELUM markup ditimpa ----------------------------
                   Penting diambil di sini: setelah innerHTML diganti, node
                   <table id="requestHistory"> yang lama ikut terbuang.
                   isDataTable() mencari node berdasarkan identitas
                   (o.nTable === t), jadi sesudah itu isDataTable('#requestHistory')
                   sudah false dan state-nya tidak bisa diambil lagi.

                   Yang penting di halaman ini: loadHistory() dipanggil ulang
                   setiap kali user menyetujui / menolak request, jadi tanpa ini
                   tabel lompat balik ke halaman 1 DAN filter pencarian ikut
                   hilang tepat di momen user sedang menelusuri history. */
                const prev = $.fn.DataTable.isDataTable('#requestHistory')
                    ? $('#requestHistory').DataTable()
                    : null;

                const keep = prev ? {
                    page: prev.page(),
                    keyword: prev.search(),
                    length: prev.page.len()
                } : null;

                fetch('../components/tables/history-request-table.php?view=transfer')
                .then(res=>res.text())
                .then(html=>{
                    /* destroy dulu, node lama masih terpasang jadi aman. Kalau
                       dibiarkan, instance-nya menumpuk di registry
                       DataTable.settings setiap loadHistory(). Sesudahnya
                       innerHTML menghapus node lama itu.

                       DULU urutannya terbalik (innerHTML dulu, destroy
                       belakangan) sehingga isDataTable() selalu false dan
                       tidak ada yang pernah ikut di-destroy. */
                    if(prev) prev.destroy();

                    document.getElementById("history-table").innerHTML = html;

                    setTimeout(() => {
                        transferIsMobile = isTransferMobile();

                        const ht = historyDataTable();

                        if(ht && keep){
                            /* Halaman di luar rentang otomatis dikembalikan
                               DataTable ke 0, jadi aman walau baris habis (mis.
                               request yang tadi baru saja disetujui/menolak
                               tidak lagi muncul di hasil filter). */
                            ht.page.len(keep.length)
                              .search(keep.keyword)
                              .page(keep.page)
                              .draw(false);
                        }
                    }, 100);
                });
            }

            /* Rebuild tabel riwayat saat pindah mobile <-> desktop.
               Re-init in-place (tanpa fetch): data yang sama tetap tampil, jadi
               tidak ada flash kosong tiap kali jendela di-resize. */
            let transferResizeTimer;
            window.addEventListener('resize', function(){
                clearTimeout(transferResizeTimer);
                transferResizeTimer = setTimeout(function(){
                    if(isTransferMobile() === transferIsMobile) return;
                    if(!$.fn.DataTable.isDataTable('#requestHistory')) return;

                    const current = $('#requestHistory').DataTable();
                    const page = current.page();
                    const keyword = current.search();
                    const length = current.page.len();

                    current.destroy();

                    transferIsMobile = isTransferMobile();
                    const ht = historyDataTable();
                    if(ht){
                        ht.page.len(length)
                          .search(keyword)
                          .page(page)
                          .draw(false);
                    }
                }, 250);
            });

            function escapeHtml(s){
                return String(s == null ? '' : s)
                    .replace(/&/g,'&amp;').replace(/</g,'&lt;')
                    .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
            }

            function fmt(n){
                return String(n || 0).replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            // TAMPILKAN / SEMBUNYIKAN DETAIL PRODUK
            // ctrl = kepala kartu (satu tombol disclosure untuk seluruh header)
            function toggleRequestItems(ctrl){
                const card = ctrl.closest('.request-card');
                if(!card) return;

                const items = card.querySelector('.request-items');
                if(!items) return;

                const willOpen = !items.classList.contains('is-open');

                items.classList.toggle('is-open', willOpen);
                ctrl.classList.toggle('is-open', willOpen);
                ctrl.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
            }

            // PRINT
            function printRequest(btn){
                const tr = btn.closest('tr');
                const id = parseInt(tr.dataset.id);
                if(!id) return;
                window.open('transfer-print-pdf.php?id=' + id, '_blank');
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