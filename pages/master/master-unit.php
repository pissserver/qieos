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
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/master-convert.css?v=<?php echo filemtime(__DIR__ . '/../../css/pages/master-convert.css'); ?>">
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
                    </div>

                    <div class="table-responsive-wrap" id="unitTableContainer"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- KONVERSI UNIT -->
    <div class="row convert-last-row">
        <div class="col-md-12">
            <div class="section-card mb-4 combine-panel convert-panel">
                <div class="panel-header panel-primary combine-header convert-header">
                    <div class="panel-left">
                        <div class="panel-icon">
                            <i class="fas fa-right-left"></i>
                        </div>

                        <div>
                            <div class="panel-title">
                                Konversi Unit
                            </div>
                            <div class="panel-subtitle">
                                Atur unit base lalu tentukan isinya, misal unit base pcs: 1 karton = 100 pcs, 1 rtg = 10 pcs
                            </div>
                        </div>
                    </div>

                    <button
                        type="button"
                        class="btn btn-stock-global js-add-convert"
                        id="btnAddConvert">
                        <i class="fas fa-right-left me-2"></i>
                        Tambah Konversi
                    </button>
                </div>

                <div class="px-4 mt-3">
                    <div class="convert-alert convert-alert-info">
                        <i class="fas fa-circle-info"></i>
                        <div>Setiap satuan hanya bisa menjadi <b>unit base satu kali</b>. Unit yang sudah dipakai tidak muncul lagi saat menambah konversi baru.</div>
                    </div>
                </div>

                <div class="mt-4 px-4 combine-body convert-body" id="convertContent">
                    <div class="text-center py-4 text-secondary">
                        <i class="fas fa-spinner fa-spin"></i> Memuat konversi...
                    </div>
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

    <!-- CONVERT MODAL -->
    <div class="modal fade" id="convertModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content stock-panel border-0">

                <div class="panel-header panel-primary my-3 mx-3">
                    <div class="panel-left">
                        <div class="panel-icon">
                            <i class="fas fa-right-left"></i>
                        </div>

                        <div class="combine-head-text">
                            <div class="panel-title" id="convertModalTitle">
                                Tambah Konversi
                            </div>
                            <div class="panel-subtitle combine-modal-subtitle">
                                Pilih unit base + daftar konversinya
                            </div>
                        </div>
                    </div>

                    <button class="btn-close btn-close-white combine-modal-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <div class="mt-2 px-5 combine-modal-body convert-modal-body" id="convertFormContent"></div>
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

    $(document).ready(function(){ loadUnitTable(); loadConvertContent(); });

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

<script>
// ===== KONVERSI UNIT =====
function convertEsc(s){
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function convertUcwords(s){
    return String(s).replace(/(^|\s)\S/g, function(m){ return m.toUpperCase(); });
}

function loadConvertContent(){
    fetch('master-convert-content.php')
    .then(res => res.text())
    .then(html => { document.getElementById('convertContent').innerHTML = html; })
    .catch(() => {});
}

var CONVERT = { units: [], items: null, baseEl: null, baseLabelEl: null, countEl: null };

function convertUnitOptionsHtml(selected){
    var opts = '<option value="">Pilih satuan...</option>';
    for(var i = 0; i < CONVERT.units.length; i++){
        var u = CONVERT.units[i];
        var sel = (u === selected) ? ' selected' : '';
        opts += '<option value="' + convertEsc(u) + '"' + sel + '>' + convertEsc(convertUcwords(u)) + '</option>';
    }
    return opts;
}

function convertRowHtml(unit, qty){
    var base = CONVERT.baseEl ? CONVERT.baseEl.value : '';
    var qv = (qty === null || qty === undefined || qty === '') ? '' : convertQtyText(qty);
    return '<div class="combine-item-row convert-item-row">' +
        '<span class="convert-one">1</span>' +
        '<select name="unit[]" class="form-input">' + convertUnitOptionsHtml(unit) + '</select>' +
        '<span class="convert-factor">' +
            '<span class="cb-eq">=</span>' +
            '<input type="number" name="qty[]" class="form-input cb-qty-input" value="' + qv + '" min="0" step="any" inputmode="decimal" placeholder="qty">' +
            '<span class="convert-base-lbl">' + (base ? convertEsc(convertUcwords(base)) : '&mdash;') + '</span>' +
        '</span>' +
        '<button type="button" class="cb-row-remove" title="Hapus satuan" aria-label="Hapus satuan"><i class="fas fa-times"></i></button>' +
    '</div>';
}

function convertQtyText(n){
    var v = Number(n);
    if(!isFinite(v)) return '';
    return v.toLocaleString('id-ID', { maximumFractionDigits: 4 });
}

// ===== SELECT2 (unit base & satuan konversi) =====
function convertBaseSelect2Config(){
    return {
        width: '100%',
        placeholder: 'Pilih unit base...',
        dropdownParent: $('#convertFormContent'),
        language: { noResults: function(){ return 'Satuan tidak ditemukan'; } }
    };
}

function convertUnitSelect2Config(){
    return {
        width: '100%',
        placeholder: 'Cari / pilih satuan...',
        minimumResultsForSearch: 0,
        dropdownParent: $('#convertFormContent'),
        language: {
            noResults: function(){ return 'Satuan tidak ditemukan'; },
            searching: function(){ return 'Mencari...'; },
            inputTooShort: function(){ return 'Ketik nama satuan...'; }
        }
    };
}

function isConvertSelect2(sel){
    return !!($.fn.select2) && $(sel).hasClass('select2-hidden-accessible');
}

function destroyConvertSelect2(scope){
    let root = (scope && scope.querySelectorAll) ? scope : CONVERT.items;
    if(!root) return;
    var sels = [];
    if(root.tagName === 'SELECT'){ sels = [root]; }
    else { sels = Array.prototype.slice.call(root.querySelectorAll('select')); }
    sels.forEach(sel => { if(isConvertSelect2(sel)) $(sel).select2('destroy'); });
}

function initConvertSelect2(scope){
    if(!$.fn.select2) return;
    let root = (scope && scope.querySelectorAll) ? scope : CONVERT.items;
    if(!root) return;
    var sels = [];
    if(root.tagName === 'SELECT'){ sels = [root]; }
    else { sels = Array.prototype.slice.call(root.querySelectorAll('select')); }
    sels.forEach(sel => {
        if(isConvertSelect2(sel)) return;
        $(sel).select2(convertUnitSelect2Config());
    });
}

// Select2 bisa menyisakan container dropdown yatim (mis. clear diklik lalu
// dropdown di-rebuild). Node yatim tidak bisa ditutup lagi karena instance
// pemiliknya sudah tidak ada, jadi dibersihkan manual.
function sweepConvertSelect2Orphans(){
    if(!$.fn.select2) return;
    let live = new Set();
    document.querySelectorAll('select.select2-hidden-accessible').forEach(sel=>{
        let box = window.qieosSelect2Container(sel);
        if(box) live.add(box);
    });
    Array.prototype.forEach.call(document.body.children, node=>{
        if(!node.classList || !node.classList.contains('select2-container')) return;
        if(live.has(node)) return;
        node.remove();
    });
}
$(document).on('click', sweepConvertSelect2Orphans);

function convertRefreshBaseLabels(){
    var base = CONVERT.baseEl ? CONVERT.baseEl.value : '';
    var txt = base ? convertUcwords(base) : '\u2014';

    if(CONVERT.baseLabelEl) CONVERT.baseLabelEl.textContent = txt;

    if(CONVERT.items){
        CONVERT.items.querySelectorAll('.convert-base-lbl').forEach(function(el){ el.textContent = txt; });
        // Satuan yang sama dengan unit base tidak boleh dipakai sebagai konversi
        CONVERT.items.querySelectorAll('select').forEach(function(sel){
            Array.prototype.forEach.call(sel.options, function(o){
                if(!o.value) return;
                o.disabled = !!base && o.value === base;
            });
            if(base && sel.value === base){
                sel.value = '';
                $(sel).val('').trigger('change.select2');
            }
        });
    }
}

function convertRecount(){
    if(!CONVERT.countEl) return;
    var n = CONVERT.items ? CONVERT.items.querySelectorAll('.combine-item-row').length : 0;
    CONVERT.countEl.textContent = n + ' satuan';
}

function addConvertRow(unit, qty){
    if(!CONVERT.items) return null;
    var wrap = document.createElement('div');
    wrap.innerHTML = convertRowHtml(unit, qty);
    var row = wrap.firstChild;
    CONVERT.items.appendChild(row);
    initConvertSelect2(row);
    convertRefreshBaseLabels();
    convertRecount();
    return row;
}

function initConvertForm(){
    var dataEl = document.getElementById('convertUnitData');
    if(!dataEl) return;
    CONVERT.units = JSON.parse(dataEl.getAttribute('data-units') || '[]');
    CONVERT.items = document.getElementById('convertItems');
    CONVERT.baseEl = document.getElementById('convertBaseSelect');
    CONVERT.baseLabelEl = document.getElementById('convertBaseLabel');
    CONVERT.countEl = document.getElementById('convertCount');

    if(CONVERT.items){
        destroyConvertSelect2(CONVERT.items);
        CONVERT.items.innerHTML = '';
    }

    if(CONVERT.baseEl && !isConvertSelect2(CONVERT.baseEl)){
        $(CONVERT.baseEl).select2(convertBaseSelect2Config());
    }

    var conv = JSON.parse(dataEl.getAttribute('data-convert') || 'null');
    if(conv){
        if(CONVERT.baseEl) $(CONVERT.baseEl).val(conv.base_unit || '').trigger('change');
        var items = conv.items || [];
        if(items.length > 0){ items.forEach(function(it){ addConvertRow(it.unit, it.qty); }); }
        else { addConvertRow('', ''); }
    } else {
        addConvertRow('', '');
    }

    convertRefreshBaseLabels();
    convertRecount();
}

function openConvertModal(id){
    var url = 'master-convert-form.php';
    if(id) url += '?id=' + encodeURIComponent(id);

    var titleEl = document.getElementById('convertModalTitle');
    if(titleEl) titleEl.textContent = id ? 'Edit Konversi' : 'Tambah Konversi';

    // form konversi sebelumnya masih punya select2 hidup di dalam modal
    destroyConvertSelect2(document.getElementById('convertFormContent'));

    $('#convertModal').modal('show');
    document.getElementById('convertFormContent').innerHTML =
        '<div class="text-center py-5"><i class="fas fa-spinner fa-spin fa-2x text-secondary"></i></div>';
    fetch(url)
    .then(res => res.text())
    .then(html => {
        document.getElementById('convertFormContent').innerHTML = html;
        initConvertForm();
    });
}

$(document).on('click', '.js-add-convert', function(){ openConvertModal(); });
$(document).on('click', '.convert-edit', function(){ openConvertModal(this.getAttribute('data-id')); });

// Dropdown select2 konversi nempel di dalam modal, jadi dibersihkan saat ditutup
document.getElementById('convertModal').addEventListener('hidden.bs.modal', function(){
    destroyConvertSelect2(document.getElementById('convertFormContent'));
    sweepConvertSelect2Orphans();
});

$(document).on('click', '#convertItems .cb-row-remove', function(){
    var row = this.closest('.combine-item-row');
    if(row){
        destroyConvertSelect2(row);
        row.remove();
    }
    convertRecount();
});

$(document).on('click', '#btnConvertAdd', function(){ addConvertRow('', ''); });

$(document).on('change', '#convertBaseSelect', function(){ convertRefreshBaseLabels(); });

$(document).on('submit', '#convertForm', function(e){
    e.preventDefault();

    if(CONVERT.baseEl && !CONVERT.baseEl.value){
        QToast('Gagal', 'Pilih unit base terlebih dahulu', 'error');
        if(isConvertSelect2(CONVERT.baseEl)){ try{ $(CONVERT.baseEl).select2('open'); }catch(err){} }
        return;
    }

    // validasi manual baris konversi (select2 menyembunyikan <select> asli)
    var firstEmpty = null;
    $('#convertItems select').each(function(){
        if(!this.value && !firstEmpty) firstEmpty = this;
    });
    if(firstEmpty){
        QToast('Gagal', 'Pilih satuan untuk semua baris konversi', 'error');
        setTimeout(function(){
            if(isConvertSelect2(firstEmpty)){ try{ $(firstEmpty).select2('open'); }catch(err){} }
        }, 0);
        return;
    }

    var formData = new FormData(this);
    var idIn = document.getElementById('convertIdInput');
    var action = idIn ? 'edit' : 'store';
    fetch('master-convert-action.php?action=' + action, {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(res => {
        if(res.status === 'success'){
            QToast('Berhasil', res.message, 'success');
            $('#convertModal').modal('hide');
            loadConvertContent();
        } else {
            QToast('Gagal', res.message || 'Terjadi kesalahan', 'error');
        }
    })
    .catch(() => { QToast('Error', 'Gagal menyimpan konversi', 'error'); });
});

$(document).on('click', '.convert-delete', function(){
    var id = this.getAttribute('data-id');
    var card = document.getElementById('convertCard' + id);
    var name = card ? card.querySelector('.combine-card-name').textContent : 'konversi ini';
    QConfirm('Hapus Konversi?', 'Konversi "' + name + '" akan dihapus.', {
        confirmText: 'Hapus',
        icon: 'fa-trash-can',
        confirmClass: 'q-confirm-btn-danger',
        iconClass: 'q-confirm-icon-danger'
    }).then(function(ok){
        if(!ok) return;
        fetch('master-convert-action.php?action=destroy', {
            method: 'POST',
            body: new URLSearchParams({ id: id })
        })
        .then(res => res.json())
        .then(res => {
            if(res.status === 'success'){
                QToast('Terhapus', 'Konversi berhasil dihapus', 'success');
                loadConvertContent();
            } else {
                QToast('Gagal', res.message || 'Terjadi kesalahan', 'error');
            }
        });
    });
});
</script>

</body>
</html>
