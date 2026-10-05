<?php
include '../../sessions/session.php';
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Update - Qieos</title>
    <?php include '../../script/headscript.php'; ?>

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/update.css?v=<?php echo filemtime(__DIR__ . '/../../css/pages/update.css'); ?>">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/components/datatable-premium.css?v=<?php echo filemtime(__DIR__ . '/../../css/components/datatable-premium.css'); ?>">
</head>

<body>
<?php include '../components/sidebar.php'; ?>
<main class="content">
<?php include '../components/navbar.php'; ?>

<div class="container-fluid px-0 mt-4">

    <!-- HEADER -->
    <!-- <div class="stock-header mt-5">
        <div>
            <h3>Stok Gudang</h3>
            <p>Monitoring stok produk dan FIFO layer secara realtime</p>
        </div>

        <div class="header-icon">
            <i class="fas fa-warehouse"></i>
        </div>
    </div> -->

    <div class="row mb-4">
        <div class="col-md-12">
            <!-- Main Table -->
            <div class="section-card mb-4 mt-4">
                <div class="panel-header panel-primary">
                    <div class="panel-left">
                        <div class="panel-icon">
                            <i class="fas fa-rocket"></i>
                        </div>

                        <div>
                            <div class="panel-title">
                                Update
                            </div>
                            <div class="panel-subtitle">
                                Riwayat pembaruan sistem
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 px-4">
                    <!-- Button Add -->
                    <?php if($user['role'] == 'developer') : ?>
                    <div id="btnContainer" style="display:none;">
                        <button
                            type="button"
                            class="btn mu-add-btn"
                            id="btnAddUpdate">
                            <i class="fas fa-plus me-2"></i>
                            Tambah Log Update
                        </button>
                    </div>
                    <?php endif; ?>
                    
                    <!-- TABLE -->
                    <div id="updateTableContainer" class="dt-premium"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add MODAL -->
    <div class="modal fade" id="addUpdateModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content stock-panel border-0">

                <div class="panel-header panel-dark my-3 mx-3 mp-add-header">
                    <div class="panel-left">
                        <div class="panel-icon">
                            <i class="fas fa-rocket"></i>
                        </div>

                        <div class="mp-add-head-text">
                            <div class="panel-title">
                                Tambah Log Update
                            </div>
                            <div class="panel-subtitle">
                                Lengkapi data dan detail pembaruan
                            </div>
                        </div>
                    </div>

                    <button class="btn-close btn-close-white mp-add-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="mt-2 px-5 mp-add-body" id="addUpdateContent"></div>
            </div>
        </div>
    </div>

    <!-- EDIT MODAL -->
    <div class="modal fade" id="editUpdateModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content stock-panel border-0">

                <div class="panel-header panel-dark my-3 mx-3 mp-add-header">
                    <div class="panel-left">
                        <div class="panel-icon">
                            <i class="fas fa-rocket"></i>
                        </div>

                        <div class="mp-add-head-text">
                            <div class="panel-title">
                                Edit Log Update
                            </div>
                            <div class="panel-subtitle">
                                Perbarui data dan detail pembaruan
                            </div>
                        </div>
                    </div>

                    <button class="btn-close btn-close-white mp-add-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="mt-2 px-5 mp-add-body" id="editUpdateContent"></div>
            </div>
        </div>
    </div>

    <!-- Show Details -->
    <div class="update-overlay" id="updateOverlay">

        <div class="update-modal">

            <div class="update-header">

                <div>
                    <div class="update-icon">
                        <i class="fas fa-rocket"></i>
                    </div>

                    <h4 id="updateTitle"></h4>

                    <div class="update-meta">

                        <span id="updateVersion"></span>

                        <span id="updateType" style="color: #000;"></span>

                        <span id="updateDate"></span>

                    </div>

                </div>

                <button class="closeUpdate">
                    <i class="fas fa-times"></i>
                </button>

            </div>

            <div class="update-body">

                <h6 class="mb-3">
                    <i class="fas fa-sparkles me-2"></i>
                    What's New
                </h6>

                <div id="updateDetailList" class="mb-5"></div>

            </div>

        </div>

    </div>
</div>
</main>

<?php include '../../script/footscript.php'; ?>

<script src="<?php echo BASE_URL; ?>/script/datatable-compact.js?v=<?php echo filemtime(__DIR__ . '/../../script/datatable-compact.js'); ?>"></script>

<script>
    /* ---------- LOAD TABLE ----------

       Breakpoint, pager, dan chip info sekarang milik helper bersama
       script/datatable-compact.js, sama seperti halaman Master, Purchasing,
       Stok Gudang, Mutasi, Transfer, Sales, dan Detail Tenant. Angka 575.98
       tidak lagi ditulis ulang di file ini. */
    function isUpdateMobile(){
        return MP_TABLE.isMobile();
    }

    let updateIsMobile = isUpdateMobile();

    function updateDataTableOptions(){
        const mobile = isUpdateMobile();

        const options = {
            pageLength: 5,
            lengthMenu: [[5,10,25,50],[5,10,25,50]],

            /* Pager kustom dari script/datatable-compact.js: 3 nomor di
               mobile, 5 di tablet/desktop, halaman pertama & terakhir dikunci
               di dua ujung, dan "..." hanya muncul kalau memang ada nomor
               yang disembunyikan.

               DULU halaman ini tidak menyebut pagingType sama sekali, jadi
               DataTables memakai bawaannya "full_numbers" - window-nya di
               numbers_length (default 7), yaitu 7 pil di tengah daftar. */
            pagingType: "mp_compact",

            /* Chip "Menampilkan 1-5 dari 137 log update" + "dari N log update"
               kalau sedang difilter. DULU tidak ada infoCallback, jadi yang
               tampil baris bawaan berbahasa Inggris
               ("Showing 1 to 5 of 137 entries"). */
            infoCallback: MP_TABLE.infoCallback('log update'),

            /* Opsi "responsive: true" DIHAPUS. Opsi itu hanya dibaca oleh
               extension Responsive (responsive.dataTables.min.js) yang TIDAK
               dimuat di script/headscript.php - yang ada hanya
               jquery.dataTables.min.js + dataTables.bootstrap5.min.js, jadi
               nilainya diam-diam diabaikan. Responsif di halaman ini
               ditangani manual: tukar "dom" saat breakpoint mobile (lihat
               bawah) + gaya kartu mobile di css/pages/update.css. */
            autoWidth: false,

            /* Urutan mengikuti ORDER BY di update-table.php. */
            order: [],

            language:{
                search:"",
                searchPlaceholder:"Cari log update...",

                /* Panah Previous/Next memakai glyph supaya tidak memakan
                   ruang di baris pager yang sempit. Aksesibilitas tetap
                   terjaga lewat aria-label bahasa Indonesia di oAria. */
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
                        <div class="empty-title">Log update tidak ditemukan</div>
                        <div class="empty-sub">
                            Coba gunakan kata kunci lain
                        </div>
                    </div>
                `,
                emptyTable: `
                    <div class="empty-search">
                        <img src="../../assets/img/illustrations/empty-data.png" class="empty-img">
                        <div class="empty-title">Belum ada data log update</div>
                        <div class="empty-sub">
                            Silakan tambahkan log update terlebih dahulu
                        </div>
                    </div>
                `
            }
        };

        /* Mobile pakai "fltip": search + show entries + tabel + baris info chip
           + pagination, semuanya dikurpose di tengah baris.

           "dom" HANYA di-set untuk mobile. Di desktop/tablet TIDAK di-set,
           sama seperti halaman lain, jadi wrapper .row + .col-* bawaan
           DataTables Bootstrap 5 yang dipakai:

             <'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>><'row dt-row'
             <'col-sm-12'tr>><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>

           DULU "dom" tidak pernah di-set sama sekali, termasuk di mobile,
           sehingga baris panjang desktop ikut terpakai di layar 360px dan
           kolom "Show n entries" ikut memutar halaman. */
        if(mobile) options.dom = "fltip";

        return options;
    }

    function updateDataTable(){
        const table = $("#stockTable");
        if(!table.length) return null;

        const dt = table.DataTable(updateDataTableOptions());

        const $filter = $('#stockTable_filter');

        if(isUpdateMobile()){

            /* ---------- MOBILE ----------

               Urutan baris di mobile:

                   search -> tombol Tambah -> show entries -> kartu

               Hanya kotak search yang dibungkus .table-action-wrapper.
               Tombol "Tambah Log Update" disisipkan SESUDAH wrapper, jadi
               ia berada di antara search dan "Show n entries" - bukan di
               atas search seperti di halaman Master, karena di sini tidak
               ada tab yang butuh tombol.toml didahulukan.

               Dulu tombol ikut masuk ke dalam wrapper bersama search.
               Di mobile premium memberi .dataTables_filter width:100%, jadi
               search melebar penuh dan tombol tergeser ke baris berikutnya
               dengan justify-content:flex-end - posisinya tidak rata. */
            if(!$filter.parent().hasClass('table-action-wrapper')){
                $filter.wrap('<div class="table-action-wrapper"></div>');
            }

            /* Sisipkan berdasarkan node wrapper-nya, bukan selector global,
               supaya tidak pernah bercabang kalau suatu saat halaman ini
               punya tabel kedua. */
            const $wrapper = $filter.parent('.table-action-wrapper');
            if($wrapper.length){
                $('#btnContainer').show().insertAfter($wrapper[0]);
            }

        }else{

            /* ---------- DESKTOP / TABLET ----------
               Search + tombol dalam satu baris, tombol nempel di kanan. */
            MP_TABLE.placeActions("#stockTable_filter", "#btnContainer");

        }

        dt.columns.adjust();

        return dt;
    }

    function loadUpdateTable() {
        $('#btnContainer').hide().insertBefore('#updateTableContainer');

        /* --- Ambil state SEBELUM markup ditimpa -------------------------------
           Penting: ini harus di atas assignment innerHTML. Setelah markup,
           node <table id="stockTable"> yang lama ikut terbuang, isDataTable()
           mencari node berdasarkan identitas (o.nTable === t), jadi sesudah
           itu isDataTable("#stockTable") sudah false dan state-nya tidak bisa
           diambil lagi.

           Ini bukan detail kecil: loadUpdateTable() dipanggil setiap kali
           tambah / edit / hapus log update. Tanpa ini tabel lompat balik ke
           halaman 1 DAN filter pencarian ikut hilang tepat setelah user
           menyelesaikan aksi. */
        const prev = $.fn.DataTable.isDataTable('#stockTable')
            ? $('#stockTable').DataTable()
            : null;

        const keep = prev ? {
            page: prev.page(),
            keyword: prev.search(),
            length: prev.page.len()
        } : null;

        fetch('update-table.php')
        .then(res => res.text())
        .then(html => {
            /* destroy dulu, node lama masih terpasang jadi aman. Kalau
               dibiarkan, instance-nya menumpuk di registry
               DataTable.settings setiap loadUpdateTable().

               DULU urutannya terbalik (innerHTML dulu, destroy belakangan)
               sehingga isDataTable() selalu false dan tidak ada yang pernah
               ikut di-destroy. */
            if(prev) prev.destroy();

            document.getElementById('updateTableContainer').innerHTML = html;

            updateIsMobile = isUpdateMobile();

            const dt = updateDataTable();

            if(dt && keep){
                /* Halaman di luar rentang otomatis dikembalikan DataTable
                   ke 0, jadi aman walau baris habis. */
                dt.page.len(keep.length)
                  .search(keep.keyword)
                  .page(keep.page)
                  .draw(false);
            }
        });
    }

    /* ---------- REBUILD KETIKA GANTI MOBILE / DESKTOP ----------

       Halaman ini sebelumnya tidak punya handler sama sekali, sehingga tabel
       yang sudah dibangun dengan dom mobile tidak pernah dibangun ulang -
       kontrolnya tetap versi mobile padahal lebar jendela sudah berubah. */
    let updateResizeTimer;
    window.addEventListener('resize', function(){
        clearTimeout(updateResizeTimer);
        updateResizeTimer = setTimeout(function(){
            if(isUpdateMobile() === updateIsMobile) return;
            if(!$.fn.DataTable.isDataTable('#stockTable')) return;

            const current = $('#stockTable').DataTable();

            const keep = {
                page: current.page(),
                keyword: current.search(),
                length: current.page.len()
            };

            current.destroy();

            updateIsMobile = isUpdateMobile();

            const dt = updateDataTable();

            if(dt){
                dt.page.len(keep.length)
                  .search(keep.keyword)
                  .page(keep.page)
                  .draw(false);
            }
        }, 250);
    });

    $(document).ready(function(){
        loadUpdateTable();
    });
</script>

<!-- Script Add -->
<script>
    // Add Modal
    $(document).on('click','#btnAddUpdate',function(){

        $('#addUpdateModal').modal('show');

        document.getElementById('addUpdateContent').innerHTML = `
            <div class="text-center py-5">
                <i class="fas fa-spinner fa-spin fa-2x text-secondary"></i>
            </div>
        `;

        fetch('update-add.php')
        .then(res => res.text())
        .then(html => {
            document.getElementById('addUpdateContent').innerHTML = html;
        });

    });

    // Add Details Update Description
    $(document).ready(function () {

        $(document).on("click", "#addDescription", function () {

            let html = `
                <div class="row description-row mb-3">

                    <div class="col-md-11">

                        <div class="input-group-modern">

                            <div class="input-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>

                            <input
                                type="text"
                                name="description[]"
                                class="form-control"
                                placeholder="Masukkan deskripsi update"
                                required>

                        </div>

                    </div>

                    <div class="col-md-1">

                        <button
                            type="button"
                            class="btn btn-danger w-100 removeDescription">

                            <i class="fas fa-trash"></i>

                        </button>

                    </div>

                </div>
            `;

            $("#descriptionContainer").append(html);

        });

        $(document).on("click", ".removeDescription", function () {

            if ($(".description-row").length <= 1) {
                return;
            }

            $(this).closest(".description-row").remove();

        });

    });

    // Add Action
    $(document).on('submit','#addUpdateForm',function(e){
        e.preventDefault();

        let formData = new FormData(this);

        fetch('update-action.php?action=store',{
            method:'POST',
            body:formData
        })
        .then(res => res.json())
        .then(res => {

            if(res.status === 'success'){

                QToast({
                    title:'Berhasil',
                    message:'Data berhasil ditambahkan',
                    type:'success'
                });

                $('#addUpdateModal').modal('hide');

                setTimeout(() => {
                    loadUpdateTable();
                }, 1000);

            }else{

                QToast({
                    title:'Gagal',
                    message:res.message || 'Terjadi kesalahan',
                    type:'error'
                });

            }

        })
        .catch(() => {
            QToast(
                'Error',
                'Gagal memproses tambah data',
                'error'
            );
        });
    });
</script>

<!-- Script Edit -->
<script>
    // OPEN EDIT MODAL
    $(document).on('click','.editUpdateBtn',function(){

        let id = $(this).data('id');

        $('#editUpdateModal').modal('show');

        document.getElementById('editUpdateContent').innerHTML = `
            <div class="text-center py-5">
                <i class="fas fa-spinner fa-spin fa-2x text-secondary"></i>
            </div>
        `;

        fetch('update-edit.php?id=' + id)
        .then(res => res.text())
        .then(html => {
            document.getElementById('editUpdateContent').innerHTML = html;
        });

    });

    // Add Details Update Description
    $(document).on("click", "#addDescriptionEdit", function () {

        $("#descriptionContainer").append(`
            <div class="row description-row mb-3">

                <div class="col-md-11">

                    <div class="input-group-modern">

                        <div class="input-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>

                        <input
                            type="text"
                            name="description[]"
                            class="form-control"
                            placeholder="Masukkan deskripsi update"
                            required>

                    </div>

                </div>

                <div class="col-md-1">

                    <button
                        type="button"
                        class="btn btn-danger w-100 removeDescription">

                        <i class="fas fa-trash"></i>

                    </button>

                </div>

            </div>
        `);

    });

    $(document).on("click", ".removeDescriptionEdit", function () {

        if ($(".description-row").length > 1) {
            $(this).closest(".description-row").remove();
        }

    });

    // Edit Action
    $(document).on('submit','#editUpdateForm',function(e){
        e.preventDefault();

        let formData = new FormData(this);
        let id = formData.get('id');

        fetch('update-action.php?action=update&id='+id,{
            method:'POST',
            body:formData
        })
        .then(res => res.json())
        .then(res => {

            if(res.status === 'success'){

                QToast({
                    title:'Berhasil',
                    message:'Data berhasil diperbarui',
                    type:'success'
                });

                $('#editUpdateModal').modal('hide');

                setTimeout(() => {
                    loadUpdateTable();
                }, 1000);

            }else{

                QToast({
                    title:'Gagal',
                    message:res.message || 'Terjadi kesalahan',
                    type:'error'
                });

            }

        })
        .catch(() => {
            QToast(
                'Error',
                'Gagal memproses update',
                'error'
            );
        });
    });
</script>

<!-- Script Delete -->
<script>
    // Delete Action
    $(document).on('click','.deleteUpdateBtn',function(){

        let id = $(this).data('id');
        let name = $(this).data('name');
        let version = $(this).data('version');

        QConfirm('Hapus Log Update?', 'Update "' + name + ' v' + version + '" akan dihapus permanen.', {confirmText:'Hapus', icon:'fa-trash-can', confirmClass:'q-confirm-btn-danger', iconClass:'q-confirm-icon-danger'}).then(function(ok){
            if(ok){
                fetch('update-action.php?action=destroy', {
                    method: 'POST',
                    body: new URLSearchParams({ id: id })
                })
                .then(res=>res.json())
                .then(res=>{

                    if(res.status==='success'){

                        QToast({
                            title:'Terhapus',
                            message:'Data berhasil dihapus',
                            type:'success'
                        });

                        setTimeout(() => {
                            loadUpdateTable();
                        }, 1000);
                    }
                });
            }
        });
    });
</script>


<!-- Script Show Details -->
<script>
    $(document).on("click", ".showUpdateBtn", function () {

        let id = $(this).data("id");

        $.get("update-detail.php", { id: id }, function (res) {

            if (res.status != "success") {
                QToast("Error", res.message, "error");
                return;
            }

            $("#updateTitle").text(res.update_name);

            $("#updateVersion").html(`
                <span class="stock-badge">${res.update_version}</span>
            `);

            $("#updateType").html(res.badge);
            $("#updateDate").text(res.update_date);

            let html = "";

            res.details.forEach(function (item) {

                html += `
                    <div class="update-item">
                        <i class="fas fa-check-circle"></i>
                        <div>${item.description}</div>
                    </div>
                `;

            });

            $("#updateDetailList").html(html);

            $("#updateOverlay")
            .css("display","flex")
            .hide()
            .fadeIn(200);

        }, "json");

    });

    $(document).on("click", ".closeUpdate", function () {
        $("#updateOverlay").fadeOut(150);
    });

    $(document).on("click", "#updateOverlay", function (e) {

        if (e.target === this) {
            $(this).fadeOut(150);
        }

    });
</script>


</body>
</html>
