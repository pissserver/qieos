<?php
include '../../sessions/session.php';
?>

<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Master Supplier - Qieos</title>
    <?php include '../../script/headscript.php'; ?>

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/master-product.css?v=<?php echo filemtime(__DIR__ . '/../../css/pages/master-product.css'); ?>">
</head>

<body>
<?php include '../components/sidebar.php'; ?>

<main class="content">
<?php include '../components/navbar.php'; ?>

<div class="container-fluid px-0 mt-4">

    <div class="row">
        <div class="col-md-12 mb-5">
            <div class="section-card mb-4 mt-4">
                <div class="panel-header panel-primary">
                    <div class="panel-left">
                        <div class="panel-icon">
                            <i class="fas fa-truck"></i>
                        </div>

                        <div>
                            <div class="panel-title">Master Supplier</div>
                            <div class="panel-subtitle">Kelola data supplier</div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 px-4">
                    <div id="btnContainer" style="display:none;">
                        <button type="button" class="btn mu-add-btn" id="btnAddSupplier">
                            <i class="fas fa-plus me-2"></i>
                            Tambah Supplier
                        </button>
                    </div>

                    <div class="table-responsive-wrap" id="supplierTableContainer"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add MODAL -->
    <div class="modal fade" id="addSupplierModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content stock-panel border-0">
                <div class="panel-header panel-dark my-3 mx-3 mp-add-header">
                    <div class="panel-left">
                        <div class="panel-icon"><i class="fas fa-plus"></i></div>
                        <div class="mp-add-head-text">
                            <div class="panel-title">Tambah Supplier</div>
                            <div class="panel-subtitle">Tambah nama, telepon, dan alamat supplier</div>
                        </div>
                    </div>
                    <button class="btn-close btn-close-white mp-add-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="mt-2 px-5 mp-add-body" id="addSupplierContent"></div>
            </div>
        </div>
    </div>

    <!-- EDIT MODAL -->
    <div class="modal fade" id="editSupplierModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content stock-panel border-0">
                <div class="panel-header panel-dark my-3 mx-3 mp-add-header">
                    <div class="panel-left">
                        <div class="panel-icon"><i class="fas fa-edit"></i></div>
                        <div class="mp-add-head-text">
                            <div class="panel-title">Edit Supplier</div>
                            <div class="panel-subtitle">Ubah informasi supplier</div>
                        </div>
                    </div>
                    <button class="btn-close btn-close-white mp-add-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="mt-2 px-5 mp-add-body" id="editSupplierContent"></div>
            </div>
        </div>
    </div>

</div>
</main>

<?php include '../../script/footscript.php'; ?>

<!-- Pager ringkas + chip info + wrapper search/tombol (helper yang sama
     dengan master-product.php) -->
<script src="<?php echo BASE_URL; ?>/script/datatable-compact.js?v=<?php echo filemtime(__DIR__ . '/../../script/datatable-compact.js'); ?>"></script>

<script>
    function isSupplierMobile(){
        return MP_TABLE.isMobile();
    }

    let supplierIsMobile = isSupplierMobile();

    function supplierDataTable(){
        const mobile = isSupplierMobile();

        const options = {
            /* Panjang halaman SAMA di semua breakpoint (5), dulu mobile 4 -
               bikin angka "4" muncul di dropdown dan bikin melompat saat
               layar dirotasi. Daftar opsi juga sama supaya user tidak
               bingung mana yang aktif. */
            pageLength: 5,
            lengthMenu: [[5,10,25,50],[5,10,25,50]],

            /* 3 nomor di mobile, 5 di tablet/desktop + halaman 1 & terakhir
               dikunci di ujung (lihat script/datatable-compact.js). */
            pagingType: "mp_compact",
            searchDelay: 250,

            /* Chip "Menampilkan 1-5 dari 42 supplier" + jumlah asal data saat
               sedang difilter. Sama persis dengan produk. */
            infoCallback: MP_TABLE.infoCallback('supplier'),

            /* Opsi "responsive: true" DIHAPUS. Extension Responsive tidak
               dimuat di headscript.php, jadi nilainya diam-diam diabaikan.
               Responsif ditangani manual lewat "dom" + .table-responsive-wrap. */
            autoWidth: false,
            language:{
                search:"",
                searchPlaceholder:"Cari supplier...",

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

                zeroRecords: '<div class="empty-search"><img src="../../assets/img/illustrations/empty-data.png" class="empty-img"><div class="empty-title">Supplier tidak ditemukan</div><div class="empty-sub">Coba gunakan kata kunci lain</div></div>',
                emptyTable: '<div class="empty-search"><img src="../../assets/img/illustrations/empty-data.png" class="empty-img"><div class="empty-title">Belum ada data supplier</div><div class="empty-sub">Silakan tambahkan supplier terlebih dahulu</div></div>'
            }
        };

        /* Mobile: "f" search, "l" show entries, "t" tabel, "i" info,
           "p" pagination. Di produk mobile "l" & "i" sengaja disembunyikan,
           tapi di supplier keduanya diminta tampil.

           "dom" HANYA di-set untuk mobile (sama seperti produk). Di
           desktop/tablet default DataTables Bootstrap 5 yang dipakai:
           "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>><'row...>"
           Wrapper row/col itulah yang menaruh "Show entries" (l) dan
           search + tombol (f) dalam satu baris di tablet/desktop. Kalau
           "dom" di-set manual juga di desktop, wrapper tersebut hilang dan
           search/tombol turun ke baris sendiri - layout premium yang sudah
           rapi jadi berantakan.

           Karena "dom" mobile dipakai langsung tanpa wrapper row/col, jarak
           antar kontrol (length di atas tabel, info & pagination di
           bawahnya) ditulis manual di CSS - lihat blok "SHOW ENTRIES +
           BARIS INFO DI MOBILE (khusus supplier)" di
           css/pages/master-product.css. */
        if(mobile) options.dom = "fltip";

        const dt = $('#stockTable').DataTable(options);
        dt.columns.adjust();
        MP_TABLE.placeActions('#stockTable_filter', '#btnContainer');
        return dt;
    }

    function loadSupplierTable(){
        /* --- Ambil state SEBELUM markup ditimpa -------------------------
           Dulu state tidak disimpan, jadi setiap loadSupplierTable() (setelah
           tambah / ubah / hapus supplier) tabel lompat balik ke halaman 1 DAN
           filter pencarian ikut hilang.

           Harus diambil DI SINI: begitu innerHTML diganti, node
           <table id="stockTable"> lama ikut terbuang. isDataTable() mencari
           node lewat identitas (o.nTable === t), jadi sesudahnya
           isDataTable('#stockTable') sudah false dan state-nya hilang. */
        const prev = $.fn.DataTable.isDataTable('#stockTable')
            ? $('#stockTable').DataTable()
            : null;

        const keep = prev ? {
            page: prev.page(),
            keyword: prev.search(),
            length: prev.page.len()
        } : null;

        $('#btnContainer').hide().insertBefore('#supplierTableContainer');

        fetch('master-supplier-table.php')
        .then(res => res.text())
        .then(html => {
            /* destroy dulu, node lama masih terpasang jadi aman; kalau
               dibiarkan, instance-nya menumpuk di registry
               DataTable.settings setiap reload. (Versi lama mengecek
               isDataTable() SESUDAH innerHTML diganti, sehingga ceknya
               selalu false dan destroy() tidak pernah jalan.) */
            if(prev) prev.destroy();

            document.getElementById('supplierTableContainer').innerHTML = html;

            setTimeout(()=>{
                supplierIsMobile = isSupplierMobile();
                const dt = supplierDataTable();

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

    $(document).ready(function(){ loadSupplierTable(); });

// ADD
$(document).on('click','#btnAddSupplier',function(){
    $('#addSupplierModal').modal('show');
    document.getElementById('addSupplierContent').innerHTML = '<div class="text-center py-5"><i class="fas fa-spinner fa-spin fa-2x text-secondary"></i></div>';
    fetch('master-supplier-add.php').then(res=>res.text()).then(html=>{ document.getElementById('addSupplierContent').innerHTML = html; });
});

$(document).on('submit','#addSupplierForm',function(e){
    e.preventDefault();
    let formData = new FormData(this);
    fetch('master-supplier-action.php?action=store',{method:'POST',body:formData})
    .then(res=>res.json()).then(res=>{
        if(res.status==='success'){ QToast('Berhasil','Supplier berhasil ditambahkan','success'); $('#addSupplierModal').modal('hide'); loadSupplierTable(); }
        else{ QToast('Gagal',res.message||'Terjadi kesalahan','error'); }
    }).catch(()=>{ QToast('Error','Gagal memproses','error'); });
});

// EDIT
$(document).on('click','.editSupplierBtn',function(){
    let id = $(this).data('id');
    $('#editSupplierModal').modal('show');
    document.getElementById('editSupplierContent').innerHTML = '<div class="text-center py-5"><i class="fas fa-spinner fa-spin fa-2x text-secondary"></i></div>';
    fetch('master-supplier-edit.php?id='+id).then(res=>res.text()).then(html=>{ document.getElementById('editSupplierContent').innerHTML = html; });
});

$(document).on('submit','#editSupplierForm',function(e){
    e.preventDefault();
    let formData = new FormData(this);
    let id = formData.get('id');
    fetch('master-supplier-action.php?action=update&id='+id,{method:'POST',body:formData})
    .then(res=>res.json()).then(res=>{
        if(res.status==='success'){ QToast('Berhasil','Supplier berhasil diperbarui','success'); $('#editSupplierModal').modal('hide'); loadSupplierTable(); }
        else{ QToast('Gagal',res.message||'Terjadi kesalahan','error'); }
    }).catch(()=>{ QToast('Error','Gagal memproses update','error'); });
});

// DELETE
$(document).on('click','.deleteSupplierBtn',function(){
    let id = $(this).data('id');
    let name = $(this).data('name');
    QConfirm('Hapus Supplier?','Data supplier '+name+' akan dihapus permanen.',{confirmText:'Hapus',icon:'fa-trash-can',confirmClass:'q-confirm-btn-danger',iconClass:'q-confirm-icon-danger'}).then(function(ok){
        if(ok){
            fetch('master-supplier-action.php?action=destroy',{method:'POST',body:new URLSearchParams({id:id})})
            .then(res=>res.json()).then(res=>{ if(res.status==='success'){ QToast('Terhapus','Data berhasil dihapus','success'); loadSupplierTable(); } });
        }
    });
});
</script>
<script>
    /* 🔥 REBUILD TABEL KETIKA GANTI MOBILE / DESKTOP

       Versi lama: destroy() -> loadSupplierTable() -> setTimeout 150ms
       untuk restore state. Itu 2 request fetch + 1 timer dan rawan race
       (dbl click saat Add/Edit bisa menyisakan request yang lebih lama
       dan menimpa markup yang baru). Sekarang re-init IN-PLACE: node
       <table> tidak diganti, jadi tidak ada fetch sama sekali. */
    let supplierResizeTimer;
    window.addEventListener('resize', function(){
        clearTimeout(supplierResizeTimer);
        supplierResizeTimer = setTimeout(function(){
            if(isSupplierMobile() === supplierIsMobile) return;
            if(!$.fn.DataTable.isDataTable('#stockTable')) return;

            const current = $('#stockTable').DataTable();
            const page = current.page();
            const keyword = current.search();
            const length = current.page.len();

            // tombol dikeluarkan dulu supaya tidak ikut hilang bersama wrapper
            $('#btnContainer').appendTo('#supplierTableContainer');
            current.destroy();

            supplierIsMobile = isSupplierMobile();
            const dt = supplierDataTable();
            dt.page.len(length).search(keyword).page(page).draw(false);
        }, 250);
    });
</script>

</body>
</html>