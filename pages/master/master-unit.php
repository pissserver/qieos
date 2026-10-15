<?php
include '../../sessions/session.php';
?>

<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Master Unit - Qieos</title>
    <?php include '../../script/headscript.php'; ?>

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/master-product.css?v=<?php echo filemtime(__DIR__ . '/../../css/pages/master-product.css'); ?>">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/master-combine.css?v=<?php echo filemtime(__DIR__ . '/../../css/pages/master-combine.css'); ?>">
</head>

<body>
<?php include '../components/sidebar.php'; ?>

<main class="content">
<?php include '../components/navbar.php'; ?>

<div class="container-fluid px-0">

    <div class="row">
        <div class="col-md-12">
            <div class="section-card mb-4 mt-4">
                <div class="panel-header panel-primary">
                    <div class="panel-left">
                        <div class="panel-icon">
                            <i class="fas fa-ruler-combined"></i>
                        </div>

                        <div>
                            <div class="panel-title">Master Unit</div>
                            <div class="panel-subtitle">Kelola satuan barang (pcs, karton, slop, dus, dan lainnya)</div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 px-4">
                    <div id="btnContainer" style="display:none;">
                        <button type="button" class="btn mu-add-btn" id="btnAddUnit">
                            <i class="fas fa-plus me-2"></i>
                            Tambah Unit
                        </button>
                        <button type="button" class="btn mu-add-btn mu-add-btn-import" id="btnImportUnit">
                            <i class="fas fa-file-import me-2"></i>Import
                        </button>
                        <a class="btn mu-add-btn mu-add-btn-export" href="master-unit-export.php">
                            <i class="fas fa-file-export me-2"></i>Export
                        </a>
                    </div>

                    <div class="table-responsive-wrap" id="unitTableContainer"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add MODAL -->
    <div class="modal fade" id="addUnitModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content stock-panel border-0">
                <div class="panel-header panel-dark my-3 mx-3 mp-add-header">
                    <div class="panel-left">
                        <div class="panel-icon"><i class="fas fa-plus"></i></div>
                        <div class="mp-add-head-text">
                            <div class="panel-title">Tambah Unit</div>
                            <div class="panel-subtitle">Tambah satuan baru, misal pcs, karton, slop, dus</div>
                        </div>
                    </div>
                    <button class="btn-close btn-close-white mp-add-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="mt-2 px-5 mp-add-body" id="addUnitContent"></div>
            </div>
        </div>
    </div>

    <!-- EDIT MODAL -->
    <div class="modal fade" id="editUnitModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content stock-panel border-0">
                <div class="panel-header panel-dark my-3 mx-3 mp-add-header">
                    <div class="panel-left">
                        <div class="panel-icon"><i class="fas fa-edit"></i></div>
                        <div class="mp-add-head-text">
                            <div class="panel-title">Edit Unit</div>
                            <div class="panel-subtitle">Ubah informasi satuan</div>
                        </div>
                    </div>
                    <button class="btn-close btn-close-white mp-add-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="mt-2 px-5 mp-add-body" id="editUnitContent"></div>
            </div>
        </div>
    </div>

    <!-- IMPORT MODAL -->
    <div class="modal fade" id="importUnitModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content stock-panel border-0">

                <div class="panel-header panel-dark my-3 mx-3 mp-add-header">
                    <div class="panel-left">
                        <div class="panel-icon">
                            <i class="fas fa-file-import"></i>
                        </div>

                        <div class="mp-add-head-text">
                            <div class="panel-title">
                                Import Unit
                            </div>
                            <div class="panel-subtitle">
                                Import data satuan dari file Excel
                            </div>
                        </div>
                    </div>

                    <button class="btn-close btn-close-white mp-add-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="mt-2 px-5 mp-add-body import-modal-body">
                    <div class="import-guide">
                        <p><i class="fas fa-info-circle"></i> Unduh template, isi daftar unit, lalu unggah kembali file tersebut.</p>
                        <p>Kolom <b>name</b> (Nama Unit) wajib diisi; <b>note</b> (Catatan) boleh kosong. Baris yang diawali <code>#</code> (mis. baris contoh) dilewati otomatis dan unit dengan nama yang sama tidak diimport dua kali.</p>
                    </div>

                    <a href="master-unit-template.php" class="btn mu-add-btn mu-add-btn-template">
                        <i class="fas fa-file-download me-2"></i>Download Template
                    </a>

                    <form id="importUnitForm" enctype="multipart/form-data">
                        <label class="import-dropzone" id="unitImportDropzone" for="importUnitFile">
                            <input type="file" id="importUnitFile" name="file" accept=".xlsx,.xls" hidden>
                            <i class="fas fa-cloud-upload-alt import-drop-ico"></i>
                            <div class="import-drop-title">Klik untuk <span>pilih file</span> Excel</div>
                            <div class="import-drop-sub">Format .xlsx / .xls &middot; maksimal 5MB</div>
                        </label>
                        <div class="import-fileinfo d-none" id="unitImportFileInfo">
                            <i class="fas fa-file-excel"></i>
                            <span id="importUnitFileName"></span>
                            <button type="button" class="import-file-remove" id="importUnitFileRemove" aria-label="Hapus file">
                                <i class="fas fa-xmark"></i>
                            </button>
                        </div>
                        <div class="mp-add-footer">
                            <button type="submit" class="btn-save" id="btnImportUnitSubmit">
                                <i class="fas fa-upload"></i> Import Sekarang
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
</main>

<?php include '../../script/footscript.php'; ?>

<!-- Pager ringkas + chip info + wrapper search/tombol (helper yang sama dengan master lain) -->
<script src="<?php echo BASE_URL; ?>/script/datatable-compact.js?v=<?php echo filemtime(__DIR__ . '/../../script/datatable-compact.js'); ?>"></script>

<script>
    function isUnitMobile(){
        return MP_TABLE.isMobile();
    }

    let unitIsMobile = isUnitMobile();

    function unitDataTable(){
        const mobile = isUnitMobile();

        const options = {
            pageLength: 5,
            lengthMenu: [[5,10,25,50],[5,10,25,50]],
            pagingType: "mp_compact",
            searchDelay: 250,
            infoCallback: MP_TABLE.infoCallback('unit'),
            autoWidth: false,
            language:{
                search:"",
                searchPlaceholder:"Cari unit...",
                paginate:{
                    previous:"&#8249;",
                    next:"&#8250;"
                },
                oAria:{
                    paginate:{
                        pageLabel:"Halaman {page}",
                        previous:"Halaman sebelumnya",
                        next:"Halaman berikutnya"
                    }
                },
                zeroRecords: '<div class="empty-search"><img src="../../assets/img/illustrations/empty-data.png" class="empty-img"><div class="empty-title">Unit tidak ditemukan</div><div class="empty-sub">Coba gunakan kata kunci lain</div></div>',
                emptyTable: '<div class="empty-search"><img src="../../assets/img/illustrations/empty-data.png" class="empty-img"><div class="empty-title">Belum ada data unit</div><div class="empty-sub">Silakan tambahkan unit terlebih dahulu</div></div>'
            }
        };

        if(mobile) options.dom = "fltip";

        const dt = $('#stockTable').DataTable(options);
        dt.columns.adjust();
        MP_TABLE.placeActions('#stockTable_filter', '#btnContainer');
        return dt;
    }

    function loadUnitTable(){
        const prev = $.fn.DataTable.isDataTable('#stockTable')
            ? $('#stockTable').DataTable()
            : null;

        const keep = prev ? {
            page: prev.page(),
            keyword: prev.search(),
            length: prev.page.len()
        } : null;

        $('#btnContainer').hide().insertBefore('#unitTableContainer');

        fetch('master-unit-table.php')
        .then(res => res.text())
        .then(html => {
            if(prev) prev.destroy();

            document.getElementById('unitTableContainer').innerHTML = html;

            setTimeout(()=>{
                unitIsMobile = isUnitMobile();
                const dt = unitDataTable();

                if(keep){
                    dt.page.len(keep.length)
                      .search(keep.keyword)
                      .page(keep.page)
                      .draw(false);
                }
            },100);
        });
    }

    $(document).ready(function(){ loadUnitTable(); });

// ADD
$(document).on('click','#btnAddUnit',function(){
    $('#addUnitModal').modal('show');
    document.getElementById('addUnitContent').innerHTML = '<div class="text-center py-5"><i class="fas fa-spinner fa-spin fa-2x text-secondary"></i></div>';
    fetch('master-unit-add.php').then(res=>res.text()).then(html=>{ document.getElementById('addUnitContent').innerHTML = html; });
});

$(document).on('submit','#addUnitForm',function(e){
    e.preventDefault();
    let formData = new FormData(this);
    fetch('master-unit-action.php?action=store',{method:'POST',body:formData})
    .then(res=>res.json()).then(res=>{
        if(res.status==='success'){ QToast('Berhasil','Unit berhasil ditambahkan','success'); $('#addUnitModal').modal('hide'); loadUnitTable(); }
        else{ QToast('Gagal',res.message||'Terjadi kesalahan','error'); }
    }).catch(()=>{ QToast('Error','Gagal memproses','error'); });
});

// EDIT
$(document).on('click','.editUnitBtn',function(){
    let id = $(this).data('id');
    $('#editUnitModal').modal('show');
    document.getElementById('editUnitContent').innerHTML = '<div class="text-center py-5"><i class="fas fa-spinner fa-spin fa-2x text-secondary"></i></div>';
    fetch('master-unit-edit.php?id='+id).then(res=>res.text()).then(html=>{ document.getElementById('editUnitContent').innerHTML = html; });
});

$(document).on('submit','#editUnitForm',function(e){
    e.preventDefault();
    let formData = new FormData(this);
    let id = formData.get('id');
    fetch('master-unit-action.php?action=update&id='+id,{method:'POST',body:formData})
    .then(res=>res.json()).then(res=>{
        if(res.status==='success'){ QToast('Berhasil','Unit berhasil diperbarui','success'); $('#editUnitModal').modal('hide'); loadUnitTable(); }
        else{ QToast('Gagal',res.message||'Terjadi kesalahan','error'); }
    }).catch(()=>{ QToast('Error','Gagal memproses update','error'); });
});

// DELETE
$(document).on('click','.deleteUnitBtn',function(){
    let id = $(this).data('id');
    let name = $(this).data('name');
    QConfirm('Hapus Unit?','Unit '+name+' akan dihapus permanen.',{confirmText:'Hapus',icon:'fa-trash-can',confirmClass:'q-confirm-btn-danger',iconClass:'q-confirm-icon-danger'}).then(function(ok){
        if(ok){
            fetch('master-unit-action.php?action=destroy',{method:'POST',body:new URLSearchParams({id:id})})
            .then(res=>res.json()).then(res=>{ if(res.status==='success'){ QToast('Terhapus','Data berhasil dihapus','success'); loadUnitTable(); } });
        }
    });
});

// IMPORT
$(document).on('click','#btnImportUnit',function(){
    $('#importUnitModal').modal('show');
});
$(document).on('change','#importUnitFile',function(){
    let f=this.files && this.files[0];
    if(!f) return;
    $('#importUnitFileName').text(f.name);
    $('#unitImportFileInfo').removeClass('d-none');
    $('#unitImportDropzone').addClass('d-none');
});
$(document).on('click','#importUnitFileRemove',function(){
    $('#importUnitFile').val('');
    $('#unitImportFileInfo').addClass('d-none');
    $('#unitImportDropzone').removeClass('d-none');
});
$('#importUnitModal').on('hidden.bs.modal',function(){
    $('#importUnitForm')[0].reset();
    $('#importUnitFile').val('');
    $('#unitImportFileInfo').addClass('d-none');
    $('#unitImportDropzone').removeClass('d-none');
});
$(document).on('submit','#importUnitForm',function(e){
    e.preventDefault();
    let f = document.getElementById('importUnitFile').files && document.getElementById('importUnitFile').files[0];
    if(!f){ QToast('Perhatian','Pilih file Excel terlebih dahulu','warning'); return; }

    let formData=new FormData(this);
    let btn=document.getElementById('btnImportUnitSubmit');
    let oldHtml=btn.innerHTML;
    btn.disabled=true;
    btn.innerHTML='<i class="fas fa-spinner fa-spin"></i> Mengimpor...';

    fetch('master-unit-import.php',{method:'POST',body:formData})
    .then(res=>res.json()).then(res=>{
        btn.disabled=false;
        btn.innerHTML=oldHtml;
        if(res.status==='success'){
            $('#importUnitModal').modal('hide');
            let msg=res.inserted+' unit berhasil diimport';
            if(res.duplicated>0) msg+=', '+res.duplicated+' duplikat dilewati';
            if(res.errors && res.errors.length>0) msg+=' ('+res.errors.length+' baris gagal)';
            QToast('Import Selesai',msg,'success');
            if(res.errors && res.errors.length>0){
                console.warn('Baris gagal:',res.errors);
            }
            loadUnitTable();
        }
        else{ QToast('Gagal',res.message||'Terjadi kesalahan saat import','error'); }
    })
    .catch(()=>{
        btn.disabled=false;
        btn.innerHTML=oldHtml;
        QToast('Error','Gagal memproses import','error');
    });
});
</script>
<script>
    /* Rebuild tabel saat berganti mobile/desktop (in-place, tanpa fetch ulang) */
    let unitResizeTimer;
    window.addEventListener('resize', function(){
        clearTimeout(unitResizeTimer);
        unitResizeTimer = setTimeout(function(){
            if(isUnitMobile() === unitIsMobile) return;
            if(!$.fn.DataTable.isDataTable('#stockTable')) return;

            const current = $('#stockTable').DataTable();
            const page = current.page();
            const keyword = current.search();
            const length = current.page.len();

            $('#btnContainer').appendTo('#unitTableContainer');
            current.destroy();

            unitIsMobile = isUnitMobile();
            const dt = unitDataTable();
            dt.page.len(length).search(keyword).page(page).draw(false);
        }, 250);
    });
</script>

</body>
</html>
