<?php include '../../sessions/session.php'; ?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Master Customer - Qieos</title>
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
                        <div class="panel-icon"><i class="fas fa-users"></i></div>
                        <div>
                            <div class="panel-title">Master Customer</div>
                            <div class="panel-subtitle">Kelola data customer</div>
                        </div>
                    </div>
                </div>
                <div class="mt-4 px-4">
                    <div id="btnContainer" style="display:none;">
                        <button type="button" class="btn mu-add-btn" id="btnAddCustomer">
                            <i class="fas fa-plus me-2"></i>Tambah Customer
                        </button>
                        <button type="button" class="btn mu-add-btn mu-add-btn-import" id="btnImportCustomer">
                            <i class="fas fa-file-import me-2"></i>Import
                        </button>
                        <a class="btn mu-add-btn mu-add-btn-export" href="master-customer-export.php">
                            <i class="fas fa-file-export me-2"></i>Export
                        </a>
                    </div>
                    <div class="table-responsive-wrap" id="customerTableContainer"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add MODAL -->
    <div class="modal fade" id="addCustomerModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content stock-panel border-0">
                <div class="panel-header panel-dark my-3 mx-3 mp-add-header">
                    <div class="panel-left">
                        <div class="panel-icon"><i class="fas fa-plus"></i></div>
                        <div class="mp-add-head-text">
                            <div class="panel-title">Tambah Customer</div>
                            <div class="panel-subtitle">Tambah nama dan nomor telepon customer</div>
                        </div>
                    </div>
                    <button class="btn-close btn-close-white mp-add-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="mt-2 px-5 mp-add-body" id="addCustomerContent"></div>
            </div>
        </div>
    </div>

    <!-- IMPORT MODAL -->
    <div class="modal fade" id="importCustomerModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content stock-panel border-0">
                <div class="panel-header panel-dark my-3 mx-3 mp-add-header">
                    <div class="panel-left">
                        <div class="panel-icon"><i class="fas fa-file-import"></i></div>
                        <div class="mp-add-head-text">
                            <div class="panel-title">Import Customer</div>
                            <div class="panel-subtitle">Import data customer dari file Excel</div>
                        </div>
                    </div>
                    <button class="btn-close btn-close-white mp-add-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="mt-2 px-5 mp-add-body import-modal-body">
                    <div class="import-guide">
                        <p><i class="fas fa-info-circle"></i> Unduh template, isi daftar customer, lalu unggah kembali file tersebut.</p>
                        <p>Kolom <b>name</b> (Nama) wajib diisi, kolom <b>phone</b> (No. Telepon) boleh kosong. Baris yang diawali <code>#</code> (mis. baris contoh) dilewati otomatis dan customer dengan nama yang sama tidak diimport dua kali.</p>
                    </div>

                    <a href="master-customer-template.php" class="btn mu-add-btn mu-add-btn-template">
                        <i class="fas fa-file-download me-2"></i>Download Template
                    </a>

                    <form id="importCustomerForm" enctype="multipart/form-data">
                        <label class="import-dropzone" id="importDropzone" for="importCustomerFile">
                            <input type="file" id="importCustomerFile" name="file" accept=".xlsx,.xls" hidden>
                            <i class="fas fa-cloud-upload-alt import-drop-ico"></i>
                            <div class="import-drop-title">Klik untuk <span>pilih file</span> Excel</div>
                            <div class="import-drop-sub">Format .xlsx / .xls &middot; maksimal 5MB</div>
                        </label>
                        <div class="import-fileinfo d-none" id="importFileInfo">
                            <i class="fas fa-file-excel"></i>
                            <span id="importFileName"></span>
                            <button type="button" class="import-file-remove" id="importFileRemove" aria-label="Hapus file">
                                <i class="fas fa-xmark"></i>
                            </button>
                        </div>
                        <div class="mp-add-footer">
                            <button type="submit" class="btn-save" id="btnImportSubmit">
                                <i class="fas fa-upload"></i> Import Sekarang
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- EDIT MODAL -->
    <div class="modal fade" id="editCustomerModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content stock-panel border-0">
                <div class="panel-header panel-dark my-3 mx-3 mp-add-header">
                    <div class="panel-left">
                        <div class="panel-icon"><i class="fas fa-edit"></i></div>
                        <div class="mp-add-head-text">
                            <div class="panel-title">Edit Customer</div>
                            <div class="panel-subtitle">Ubah informasi customer</div>
                        </div>
                    </div>
                    <button class="btn-close btn-close-white mp-add-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="mt-2 px-5 mp-add-body" id="editCustomerContent"></div>
            </div>
        </div>
    </div>

</div>
</main>

<?php include '../../script/footscript.php'; ?>

<!-- Pager ringkas + chip info + wrapper search/tombol (helper yang sama
     dengan master-product.php & master-supplier.php) -->
<script src="<?php echo BASE_URL; ?>/script/datatable-compact.js?v=<?php echo filemtime(__DIR__ . '/../../script/datatable-compact.js'); ?>"></script>

<script>
    function isCustomerMobile(){
        return MP_TABLE.isMobile();
    }

    let customerIsMobile = isCustomerMobile();

    function customerDataTable(){
        const mobile = isCustomerMobile();

        const options = {
            /* Panjang halaman SAMA di semua breakpoint (5), dulu mobile 4 -
               bikin angka "4" muncul di dropdown dan tabel melompat saat
               layar dirotasi. Daftar opsi juga sama supaya user tidak
               bingung mana yang aktif. */
            pageLength: 5,
            lengthMenu: [[5,10,25,50],[5,10,25,50]],

            /* 3 nomor di mobile, 5 di tablet/desktop + halaman 1 & terakhir
               dikunci di ujung (lihat script/datatable-compact.js). */
            pagingType: "mp_compact",
            searchDelay: 250,

            /* Chip "Menampilkan 1-5 dari 42 customer" + jumlah asal data saat
               sedang difilter. Sama persis dengan produk & supplier. */
            infoCallback: MP_TABLE.infoCallback('customer'),

            /* Opsi "responsive: true" DIHAPUS. Extension Responsive tidak
               dimuat di headscript.php, jadi nilainya diam-diam diabaikan.
               Responsif ditangani manual lewat "dom" + .table-responsive-wrap. */
            autoWidth: false,
            language:{
                search:"",
                searchPlaceholder:"Cari customer...",

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

                zeroRecords: '<div class="empty-search"><img src="../../assets/img/illustrations/empty-data.png" class="empty-img"><div class="empty-title">Customer tidak ditemukan</div><div class="empty-sub">Coba gunakan kata kunci lain</div></div>',
                emptyTable: '<div class="empty-search"><img src="../../assets/img/illustrations/empty-data.png" class="empty-img"><div class="empty-title">Belum ada data customer</div><div class="empty-sub">Silakan tambahkan customer terlebih dahulu</div></div>'
            }
        };

        /* Mobile: "f" search, "l" show entries, "t" tabel, "i" info,
           "p" pagination - semuanya dirender supaya user bisa langsung
           menaikkan jumlah baris dan tahu posisinya di daftar.

           "dom" HANYA di-set untuk mobile. Di desktop/tablet default
           DataTables Bootstrap 5 yang dipakai:
           "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>><'row...>"
           Wrapper row/col itulah yang menaruh "Show entries" (l) dan
           search + tombol (f) dalam satu baris. Kalau "dom" di-set manual
           juga di desktop, wrapper tersebut hilang dan search/tombol turun
           ke baris sendiri - layout premium yang sudah rapi jadi berantakan.

           Karena "dom" mobile dipakai langsung tanpa wrapper row/col, jarak
           antar kontrol (length di atas tabel, info & pagination di
           bawahnya) ditulis manual di CSS - lihat blok "SHOW ENTRIES +
           BARIS INFO DI MOBILE" di css/pages/master-product.css. */
        if(mobile) options.dom = "fltip";

        const dt = $('#stockTable').DataTable(options);
        dt.columns.adjust();
        MP_TABLE.placeActions('#stockTable_filter', '#btnContainer');
        return dt;
    }

    function loadCustomerTable(){
        /* --- Ambil state SEBELUM markup ditimpa -------------------------
           Dulu state tidak disimpan, jadi setiap loadCustomerTable() (setelah
           tambah / ubah / hapus customer) tabel lompat balik ke halaman 1 DAN
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

        $('#btnContainer').hide().insertBefore('#customerTableContainer');

        fetch('master-customer-table.php')
        .then(res => res.text())
        .then(html => {
            /* destroy dulu, node lama masih terpasang jadi aman; kalau
               dibiarkan, instance-nya menumpuk di registry
               DataTable.settings setiap reload. (Versi lama mengecek
               isDataTable() SESUDAH innerHTML diganti, sehingga ceknya
               selalu false dan destroy() tidak pernah jalan.) */
            if(prev) prev.destroy();

            document.getElementById('customerTableContainer').innerHTML = html;

            setTimeout(()=>{
                customerIsMobile = isCustomerMobile();
                const dt = customerDataTable();

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

    $(document).ready(function(){ loadCustomerTable(); });

// ADD
$(document).on('click','#btnAddCustomer',function(){
    $('#addCustomerModal').modal('show');
    document.getElementById('addCustomerContent').innerHTML='<div class="text-center py-5"><i class="fas fa-spinner fa-spin fa-2x text-secondary"></i></div>';
    fetch('master-customer-add.php').then(res=>res.text()).then(html=>{ document.getElementById('addCustomerContent').innerHTML=html; });
});
$(document).on('submit','#addCustomerForm',function(e){
    e.preventDefault();
    let formData=new FormData(this);
    fetch('master-customer-action.php?action=store',{method:'POST',body:formData})
    .then(res=>res.json()).then(res=>{
        if(res.status==='success'){ QToast('Berhasil','Customer berhasil ditambahkan','success'); $('#addCustomerModal').modal('hide'); loadCustomerTable(); }
        else{ QToast('Gagal',res.message||'Terjadi kesalahan','error'); }
    }).catch(()=>{ QToast('Error','Gagal memproses','error'); });
});

// IMPORT
$(document).on('click','#btnImportCustomer',function(){
    $('#importCustomerModal').modal('show');
});
$(document).on('change','#importCustomerFile',function(){
    let f=this.files && this.files[0];
    if(!f) return;
    $('#importFileName').text(f.name);
    $('#importFileInfo').removeClass('d-none');
    $('#importDropzone').addClass('d-none');
});
$(document).on('click','#importFileRemove',function(){
    $('#importCustomerFile').val('');
    $('#importFileInfo').addClass('d-none');
    $('#importDropzone').removeClass('d-none');
});
$('#importCustomerModal').on('hidden.bs.modal',function(){
    $('#importCustomerForm')[0].reset();
    $('#importCustomerFile').val('');
    $('#importFileInfo').addClass('d-none');
    $('#importDropzone').removeClass('d-none');
});
$(document).on('submit','#importCustomerForm',function(e){
    e.preventDefault();
    let f = document.getElementById('importCustomerFile').files && document.getElementById('importCustomerFile').files[0];
    if(!f){ QToast('Perhatian','Pilih file Excel terlebih dahulu','warning'); return; }

    let formData=new FormData(this);
    let btn=document.getElementById('btnImportSubmit');
    let oldHtml=btn.innerHTML;
    btn.disabled=true;
    btn.innerHTML='<i class="fas fa-spinner fa-spin"></i> Mengimpor...';

    fetch('master-customer-import.php',{method:'POST',body:formData})
    .then(res=>res.json()).then(res=>{
        btn.disabled=false;
        btn.innerHTML=oldHtml;
        if(res.status==='success'){
            $('#importCustomerModal').modal('hide');
            let msg=res.inserted+' customer berhasil diimport';
            if(res.duplicated>0) msg+=', '+res.duplicated+' duplikat dilewati';
            if(res.errors && res.errors.length>0) msg+=' ('+res.errors.length+' baris gagal)';
            QToast('Import Selesai',msg,'success');
            if(res.errors && res.errors.length>0){
                console.warn('Baris gagal:',res.errors);
            }
            loadCustomerTable();
        }
        else{ QToast('Gagal',res.message||'Terjadi kesalahan saat import','error'); }
    })
    .catch(()=>{
        btn.disabled=false;
        btn.innerHTML=oldHtml;
        QToast('Error','Gagal memproses import','error');
    });
});

// EDIT
$(document).on('click','.editCustomerBtn',function(){
    let id=$(this).data('id');
    $('#editCustomerModal').modal('show');
    document.getElementById('editCustomerContent').innerHTML='<div class="text-center py-5"><i class="fas fa-spinner fa-spin fa-2x text-secondary"></i></div>';
    fetch('master-customer-edit.php?id='+id).then(res=>res.text()).then(html=>{ document.getElementById('editCustomerContent').innerHTML=html; });
});
$(document).on('submit','#editCustomerForm',function(e){
    e.preventDefault();
    let formData=new FormData(this);
    let id=formData.get('id');
    fetch('master-customer-action.php?action=update&id='+id,{method:'POST',body:formData})
    .then(res=>res.json()).then(res=>{
        if(res.status==='success'){ QToast('Berhasil','Customer berhasil diperbarui','success'); $('#editCustomerModal').modal('hide'); loadCustomerTable(); }
        else{ QToast('Gagal',res.message||'Terjadi kesalahan','error'); }
    }).catch(()=>{ QToast('Error','Gagal memproses update','error'); });
});

// DELETE
$(document).on('click','.deleteCustomerBtn',function(){
    let id=$(this).data('id');
    let name=$(this).data('name');
    QConfirm('Hapus Customer?','Data customer '+name+' akan dihapus permanen.',{confirmText:'Hapus',icon:'fa-trash-can',confirmClass:'q-confirm-btn-danger',iconClass:'q-confirm-icon-danger'}).then(function(ok){
        if(ok){
            fetch('master-customer-action.php?action=destroy',{method:'POST',body:new URLSearchParams({id:id})})
            .then(res=>res.json()).then(res=>{ if(res.status==='success'){ QToast('Terhapus','Data berhasil dihapus','success'); loadCustomerTable(); } });
        }
    });
});
</script>
<script>
    /* 🔥 REBUILD TABEL KETIKA GANTI MOBILE / DESKTOP

       Versi lama: destroy() -> loadCustomerTable() -> setTimeout 150ms
       untuk restore state. Itu 2 request fetch + 1 timer dan rawan race
       (dbl click saat Add/Edit bisa menyisakan request yang lebih lama dan
       menimpa markup yang baru). Sekarang re-init IN-PLACE: node <table>
       tidak diganti, jadi tidak ada fetch sama sekali. */
    let customerResizeTimer;
    window.addEventListener('resize', function(){
        clearTimeout(customerResizeTimer);
        customerResizeTimer = setTimeout(function(){
            if(isCustomerMobile() === customerIsMobile) return;
            if(!$.fn.DataTable.isDataTable('#stockTable')) return;

            const current = $('#stockTable').DataTable();
            const page = current.page();
            const keyword = current.search();
            const length = current.page.len();

            // tombol dikeluarkan dulu supaya tidak ikut hilang bersama wrapper
            $('#btnContainer').appendTo('#customerTableContainer');
            current.destroy();

            customerIsMobile = isCustomerMobile();
            const dt = customerDataTable();
            dt.page.len(length).search(keyword).page(page).draw(false);
        }, 250);
    });
</script>
</body>
</html>