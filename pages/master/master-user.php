<?php
include '../../sessions/session.php';
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Master User - Qieos</title>
    <?php include '../../script/headscript.php'; ?>

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/master-product.css?v=<?php echo filemtime(__DIR__ . '/../../css/pages/master-product.css'); ?>">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/master-user.css?v=<?php echo filemtime(__DIR__ . '/../../css/pages/master-user.css'); ?>">
</head>

<body>
<?php include '../components/sidebar.php'; ?>
<main class="content">
<?php include '../components/navbar.php'; ?>

<div class="container-fluid px-0">
    <div class="row">
        <div class="col-md-12">

            <!-- Main Panel -->
            <div class="section-card mb-4 mt-4">
                <div class="panel-header panel-primary">
                    <div class="panel-left">
                        <div class="panel-icon">
                            <i class="fas fa-users-cog"></i>
                        </div>

                        <div>
                            <div class="panel-title">
                                Master User
                            </div>
                            <div class="panel-subtitle">
                                Kelola administrator dan staff kasir dalam satu panel
                            </div>
                        </div>
                    </div>

                    <div class="panel-header-right">
                        <div class="mu-hero-chip">
                            <i class="fas fa-shield-halved"></i>
                            <span>Terproteksi</span>
                        </div>
                    </div>
                </div>

                <div class="mt-4 px-4">
                    <!-- TABS + ADD BUTTON -->
                    <div class="mu-toolbar">
                        <div class="mu-tabs" id="userTabs">
                            <span class="mu-tab-indicator" id="tabIndicator"></span>

                            <button type="button" class="mu-tab active" data-role="administrator">
                                <span class="mu-tab-icon"><i class="fas fa-user-shield"></i></span>
                                <span>Administrator</span>
                                <span class="mu-count" id="countAdmin">0</span>
                            </button>

                            <button type="button" class="mu-tab" data-role="cashier">
                                <span class="mu-tab-icon"><i class="fas fa-user-tie"></i></span>
                                <span>Staff Kasir</span>
                                <span class="mu-count" id="countCashier">0</span>
                            </button>
                        </div>

                        <div class="mu-toolbar-right">
                            <!-- SEARCH: diisi otomatis oleh DataTable di desktop/tablet -->
                            <div class="mu-search-slot" id="muSearchSlot"></div>

                            <button type="button" class="btn mu-add-btn" id="btnAddUser">
                                <i class="fas fa-user-plus me-2"></i>
                                Tambah User
                            </button>
                        </div>
                    </div>

                    <!-- TABLE -->
                    <div class="table-responsive-wrap" id="userTableContainer">
                        <!-- Loaded via AJAX -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ADD MODAL -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content stock-panel border-0">

            <div class="panel-header panel-dark my-3 mx-3 mp-add-header">
                <div class="panel-left">
                    <div class="panel-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>

                    <div class="mp-add-head-text">
                        <div class="panel-title">
                            Tambah User
                        </div>
                        <div class="panel-subtitle">
                            Pilih role, lalu lengkapi informasi user
                        </div>
                    </div>
                </div>

                <button class="btn-close btn-close-white mp-add-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="mt-2 px-5 mp-add-body" id="addUserContent"></div>
        </div>
    </div>
</div>

<!-- EDIT MODAL -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content stock-panel border-0">

            <div class="panel-header panel-dark my-3 mx-3 mp-add-header">
                <div class="panel-left">
                    <div class="panel-icon">
                        <i class="fas fa-user-pen"></i>
                    </div>

                    <div class="mp-add-head-text">
                        <div class="panel-title">
                            Edit User
                        </div>
                        <div class="panel-subtitle">
                            Ubah role atau informasi user
                        </div>
                    </div>
                </div>

                <button class="btn-close btn-close-white mp-add-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="mt-2 px-5 mp-add-body" id="editUserContent"></div>
        </div>
    </div>
</div>

</main>

<?php include '../../script/footscript.php'; ?>

<!-- Pager ringkas + chip info (helper yang sama dengan master-product.php,
     master-supplier.php, & master-customer.php) -->
<script src="<?php echo BASE_URL; ?>/script/datatable-compact.js?v=<?php echo filemtime(__DIR__ . '/../../script/datatable-compact.js'); ?>"></script>

<script>
    let activeRole = 'administrator';

    function isUserMobile(){
        return MP_TABLE.isMobile();
    }

    let userIsMobile = isUserMobile();

    // ----- TAB INDICATOR -----
    function positionIndicator(){
        const tabs = document.querySelectorAll('.mu-tab');
        const indicator = document.getElementById('tabIndicator');
        if(!indicator || !tabs.length) return;

        const active = document.querySelector('.mu-tab.active');
        if(!active) return;

        indicator.style.left = active.offsetLeft + 'px';
        indicator.style.width = active.offsetWidth + 'px';
    }

    window.addEventListener('load', positionIndicator);
    window.addEventListener('resize', positionIndicator);

    // ----- BUILD DATATABLE -----
    function userDataTable(){
        const isCashier = activeRole === 'cashier';
        const mobile = isUserMobile();

        const options = {
            /* Panjang halaman SAMA di semua breakpoint (5), dulu mobile 4 -
               angka "4" muncul di dropdown dan tabel melompat saat layar
               dirotasi. Daftar opsi juga sama supaya tidak bingung mana
               yang aktif. */
            pageLength: 5,
            lengthMenu: [[5,10,25,50],[5,10,25,50]],

            /* 3 nomor di mobile, 5 di tablet/desktop + halaman 1 & terakhir
               dikunci di ujung (lihat script/datatable-compact.js). */
            pagingType: "mp_compact",
            searchDelay: 250,

            /* Chip info ikut menyebut role-nya, jadi "Menampilkan 1-5 dari 3
               staff kasir" tidak tercampur dengan tab administrator. */
            infoCallback: MP_TABLE.infoCallback(isCashier ? 'staff kasir' : 'administrator'),

            /* Opsi "responsive: true" DIHAPUS. Extension Responsive tidak
               dimuat di headscript.php, jadi nilainya diam-diam diabaikan.
               Responsif ditangani manual lewat "dom" + .table-responsive-wrap. */
            autoWidth: false,
            language:{
                search:"",
                searchPlaceholder: isCashier ? "Cari staff kasir..." : "Cari administrator...",

                /* Panah Previous/Next jadi glyph "‹ ›", bukan teks. Alasannya
                   lebar: pada rentang 9 pil, dua pil TEKS ("Previous"/"Next")
                   memakan ~150px dari total baris pager, sementara sisa ruang
                   di layar 320-360px cuma ~290-330px. Makanya teks diganti
                   glyph. */
                paginate:{
                    previous:"&#8249;",  // ‹
                    next:"&#8250;"       // ›
                },

                /* Label yang dibacakan screen reader tetap bahasa Indonesia,
                   jadi aksesibilitas tidak ikut hilang saat glyphnya diganti. */
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
                        <div class="empty-title">${isCashier ? 'Staff kasir' : 'Administrator'} tidak ditemukan</div>
                        <div class="empty-sub">
                            Coba gunakan kata kunci lain
                        </div>
                    </div>
                `,

                emptyTable: `
                    <div class="empty-search">
                        <img src="../../assets/img/illustrations/empty-data.png" class="empty-img">
                        <div class="empty-title">Belum ada data ${isCashier ? 'staff kasir' : 'administrator'}</div>
                        <div class="empty-sub">
                            Silakan tambahkan user terlebih dahulu
                        </div>
                    </div>
                `
            }
        };

        /* Mobile: "f" search, "l" show entries, "t" tabel, "i" info,
           "p" pagination - semuanya dirender supaya user bisa menaikkan
           jumlah baris dan tahu posisinya di daftar.

           "dom" HANYA di-set untuk mobile. Di desktop/tablet default
           DataTables Bootstrap 5 yang dipakai, dan search tetap dipindah
           ke toolbar tabs lewat .mu-search-slot (lihat catatan di bawah).

           Karena "dom" mobile dipakai langsung tanpa wrapper row/col, jarak
           antar kontrol (length di atas tabel, info & pagination di
           bawahnya) ditulis manual di CSS - lihat blok "SHOW ENTRIES +
           BARIS INFO DI MOBILE" di css/pages/master-product.css. */
        if(mobile) options.dom = "fltip";

        /* Sisa #userTable_filter dari init SEBELUMNYA dibuang dulu. Pada
           halaman lain filter selalu tinggal di dalam wrapper tabel jadi
           destroy() saja sudah cukup, tapi di sini filter pernah dipindah
           ke .mu-search-slot (di luar container). Kalau node-nya tertinggal,
           selector "#userTable_filter" jadi menunjuk dua elemen dan user
           baru mengetik di kotak yang salah.

           Aman dijalankan sebelum DataTable() karena pada titik ini
           filter milik init baru BELUM dibuat - semua node yang ada
           hanyalah sisa. */
        $('#userTableContainer').find('#userTable_filter').remove();
        $('.mu-search-slot').find('#userTable_filter').remove();

        const dt = $('#userTable').DataTable(options);
        dt.columns.adjust();

        /* Search di halaman ini tidak memakai #btnContainer seperti halaman
           Master lain: tombol "Tambah User" sudah jadi bagian dari toolbar
           tabs (.mu-toolbar-right), bukan container terpisah.

           - Desktop/tablet: search dipindah ke .mu-search-slot supaya
             duduk di samping tombol, bukan di dalam wrapper tabel.
           - Mobile: search tetap di atas tabel full width, dibungkus
             .table-action-wrapper supaya barisnya rapi. */
        const $filter = $('#userTable_filter');

        if(mobile){
            if(!$filter.parent().hasClass('table-action-wrapper')){
                $filter.wrap('<div class="table-action-wrapper"></div>');
            }
            // Mobile: search dulu, tombol di bawahnya (satu baris di dalam
            // .table-action-wrapper yang menyusun kolom full width).
            $('#btnAddUser').appendTo($filter.parent());
        } else {
            $('.mu-search-slot').empty();
            $filter.appendTo('.mu-search-slot');
            // Keluar dari mobile: kembalikan tombol ke toolbar tabs.
            $('#btnAddUser').appendTo('.mu-toolbar-right');
        }

        return dt;
    }

    // ----- LOAD TABLE -----
    function loadUserTable(role){
        /* Ganti tab = dataset berbeda. State halaman/pencarian TIDAK boleh
           ikut terbawa (mis. buka tab kasir di halaman 3 hasil cari
           "andi", lalu balik ke administrator - hasilnya harus mulai
           dari awal). Jadi state hanya disimpan kalau role-nya sama,
           yaitu reload setelah tambah / ubah / hapus. */
        const sameRole = (role === activeRole);
        activeRole = role;

        /* Ambil state SEBELUM markup ditimpa. Begitu innerHTML diganti,
           node <table id="userTable"> lama ikut terbuang; isDataTable()
           mencari node lewat identitas (o.nTable === t) jadi sesudahnya
           isDataTable('#userTable') sudah false dan state hilang. */
        const prev = $.fn.DataTable.isDataTable('#userTable')
            ? $('#userTable').DataTable()
            : null;

        /* State hanya dipulihkan kalau role-nya sama. Instance-nya sendiri
           tetap di-destroy di semua kasus - kalau tidak, ganti tab
           menyisakan instance lama di registry DataTable.settings
           (menumpuk tiap klik tab). */
        const keep = (prev && sameRole) ? {
            page: prev.page(),
            keyword: prev.search(),
            length: prev.page.len()
        } : null;

        // Search yang sempat dipindah ke toolbar harus dibuang lebih dulu,
        // supaya tidak tertinggal dan tidak dobel saat tabel dibangun ulang.
        $('.mu-search-slot').empty();

        fetch('master-user-table.php?role=' + encodeURIComponent(role))
        .then(res => res.text())
        .then(html => {
            /* destroy dulu, node lama masih terpasang jadi aman; kalau
               dibiarkan, instance-nya menumpuk di registry
               DataTable.settings setiap reload. (Versi lama mengecek
               isDataTable() SESUDAH innerHTML diganti, sehingga ceknya
               selalu false dan destroy() tidak pernah jalan.) */
            if(prev) prev.destroy();

            const container = document.getElementById('userTableContainer');
            container.innerHTML = html;

            // Update count badges
            const table = document.getElementById('userTable');
            if(table){
                const ca = table.getAttribute('data-count-admin');
                const cc = table.getAttribute('data-count-cashier');
                if(ca !== null) document.getElementById('countAdmin').textContent = ca;
                if(cc !== null) document.getElementById('countCashier').textContent = cc;
            }

            setTimeout(()=>{
                userIsMobile = isUserMobile();
                const dt = userDataTable();

                if(keep){
                    /* Halaman di luar rentang otomatis dikembalikan
                       DataTable ke 0, jadi aman walau baris habis (mis.
                       semua hasil filter ikut terhapus). */
                    dt.page.len(keep.length)
                      .search(keep.keyword)
                      .page(keep.page)
                      .draw(false);
                }
            },100);
        });
    }

    // ----- TABS -----
    $(document).on('click','.mu-tab',function(){
        if($(this).hasClass('active')) return;

        $('.mu-tab').removeClass('active');
        $(this).addClass('active');

        positionIndicator();
        loadUserTable($(this).data('role'));
    });

    // ----- ADD -----
    $(document).on('click','#btnAddUser',function(){
        $('#addUserModal').modal('show');

        document.getElementById('addUserContent').innerHTML = `
            <div class="text-center py-5">
                <i class="fas fa-spinner fa-spin fa-2x text-secondary"></i>
            </div>
        `;

        fetch('master-user-add.php?role=' + encodeURIComponent(activeRole))
        .then(res => res.text())
        .then(html => {
            document.getElementById('addUserContent').innerHTML = html;
        });
    });

    $(document).on('submit','#addUserForm',function(e){
        e.preventDefault();

        let formData = new FormData(this);

        fetch('master-user-action.php?action=store',{
            method:'POST',
            body:formData
        })
        .then(res => res.json())
        .then(res => {

            if(res.status === 'success'){

                QToast('Berhasil', 'Data berhasil ditambahkan', 'success');

                $('#addUserModal').modal('hide');

                // Switch ke tab sesuai role yang dipilih
                const role = res.role === 'staff kasir' ? 'cashier' : 'administrator';
                $('.mu-tab').removeClass('active');
                $('.mu-tab[data-role="'+role+'"]').addClass('active');
                positionIndicator();
                loadUserTable(role);

            }else{

                QToast('Gagal', res.message || 'Terjadi kesalahan', 'error');

            }
        })
        .catch(() => {
            QToast('Error', 'Gagal memproses tambah data', 'error');
        });
    });

    // ----- EDIT -----
    $(document).on('click','.editUserBtn',function(){
        let id = $(this).data('id');

        $('#editUserModal').modal('show');

        document.getElementById('editUserContent').innerHTML = `
            <div class="text-center py-5">
                <i class="fas fa-spinner fa-spin fa-2x text-secondary"></i>
            </div>
        `;

        fetch('master-user-edit.php?id=' + id)
        .then(res => res.text())
        .then(html => {
            document.getElementById('editUserContent').innerHTML = html;
        });
    });

    $(document).on('submit','#editUserForm',function(e){
        e.preventDefault();

        let formData = new FormData(this);
        let id = formData.get('id');

        fetch('master-user-action.php?action=update&id='+id,{
            method:'POST',
            body:formData
        })
        .then(res => res.json())
        .then(res => {

            if(res.status === 'success'){

                QToast('Berhasil', 'Data berhasil diperbarui', 'success');

                $('#editUserModal').modal('hide');

                const role = res.role === 'staff kasir' ? 'cashier' : 'administrator';
                $('.mu-tab').removeClass('active');
                $('.mu-tab[data-role="'+role+'"]').addClass('active');
                positionIndicator();
                loadUserTable(role);

            }else{

                QToast('Gagal', res.message || 'Terjadi kesalahan', 'error');

            }
        })
        .catch(() => {
            QToast('Error', 'Gagal memproses update', 'error');
        });
    });

    // ----- DELETE -----
    $(document).on('click','.deleteUserBtn',function(){
        let id = $(this).data('id');
        let fullname = $(this).data('fullname');

        QConfirm('Hapus User?', 'Data ' + fullname + ' akan dihapus secara permanen.', {confirmText:'Hapus', icon:'fa-trash-can', confirmClass:'q-confirm-btn-danger', iconClass:'q-confirm-icon-danger'}).then(function(ok){
            if(ok){
                fetch('master-user-action.php?action=destroy', {
                    method: 'POST',
                    body: new URLSearchParams({ id: id })
                })
                .then(res=>res.json())
                .then(res=>{

                    if(res.status==='success'){

                        QToast('Terhapus', 'Data berhasil dihapus', 'success');

                        loadUserTable(activeRole);
                    }else{
                        QToast('Gagal', res.message || 'Terjadi kesalahan', 'error');
                    }
                });
            }
        });
    });

    // ----- INIT -----
    $(document).ready(function(){
        loadUserTable('administrator');
    });

    // ----- REBUILD TABEL KETIKA GANTI MOBILE / DESKTOP -----
    /* Re-init IN-PLACE. Versi lama memanggil loadUserTable() sehingga satu
       perpindahan breakpoint = 1 fetch penuh + rebuild table, dan state
       (halaman / pencarian / panjang halaman) hilang. Sekarang node
       <table> tidak diganti, jadi tidak ada fetch sama sekali dan posisi
       user tetap terjaga. */
    let userResizeTimer;
    window.addEventListener('resize', function(){
        clearTimeout(userResizeTimer);
        userResizeTimer = setTimeout(function(){
            if(isUserMobile() === userIsMobile) return;
            if(!$.fn.DataTable.isDataTable('#userTable')) return;

            const current = $('#userTable').DataTable();
            const page = current.page();
            const keyword = current.search();
            const length = current.page.len();

            // search yang ada di toolbar dibuang dulu supaya tidak dobel
            $('.mu-search-slot').empty();
            current.destroy();

            userIsMobile = isUserMobile();
            const dt = userDataTable();
            dt.page.len(length).search(keyword).page(page).draw(false);
        }, 250);
    });
</script>

</body>
</html>