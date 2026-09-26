<?php
include '../../sessions/session.php';

// Form daftar belanja yang belum diinput qty (qty masih NULL) untuk select
$qForms = mysqli_query($conn,"
    SELECT DISTINCT p.id, p.form, p.date
    FROM purchases p
    JOIN purchase_items pi
        ON pi.purchase_id = p.id
        AND pi.deleted_at IS NULL
    WHERE p.deleted_at IS NULL
      AND pi.qty_buy IS NOT NULL
      AND pi.qty IS NULL
    ORDER BY p.date DESC, p.id DESC
");

$listForms = [];
while($f = mysqli_fetch_assoc($qForms)){
    $listForms[] = $f;
}
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Input Pembelian - Qieos</title>
<?php include '../../script/headscript.php'; ?>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/purchase.css?v=<?php echo filemtime(__DIR__.'/../../css/pages/purchase.css'); ?>">
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

</head>

<body>

<?php include '../components/sidebar.php'; ?>

<main class="content">
<?php include '../components/navbar.php'; ?>

<div class="container-fluid px-0 mt-5">
    <!-- FORM -->
    <div class="section-card mb-4">
        <div class="panel-header panel-primary">
            <div class="panel-left">
                <div class="panel-icon">
                    <i class="fas fas fa-file-alt"></i>
                </div>

                <div>
                    <div class="panel-title">
                        Form Pembelian 
                    </div>
                    <div class="panel-subtitle">
                        Pilih form daftar belanja lalu input qty & harga beli
                    </div>
                </div>
            </div>

            <div class="panel-toggle-wrap">
                <span class="toggle-label active" id="labelForm">Form</span>

                <label class="switch-toggle">
                    <input type="checkbox" id="panelToggle">
                    <span class="slider-toggle"></span>
                </label>

                <span class="toggle-label" id="labelEdit">Edit</span>
            </div>
        </div>

        <div class="mt-4 px-4">
            <div class="stock-body">

                <div id="formMode" class="panel-mode active">
                    <form id="form-stock" action="purchase-action.php?action=save_items" method="POST">

                        <!-- PILIH FORM -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="input-group-modern">
                                    <div class="input-icon">
                                        <i class="fas fa-hashtag"></i>
                                    </div>

                                    <select
                                        name="purchase_id"
                                        id="purchaseFormSelect"
                                        class="form-control"
                                        placeholder="Pilih Form Daftar Belanja">
                                        <option value=""></option>
                                        <?php foreach($listForms as $f): ?>
                                        <option value="<?= $f['id'] ?>">
                                            <?= htmlspecialchars($f['form']) ?> - <?= date('d F Y', strtotime($f['date'])) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- DAFTAR ITEM -->
                        <div class="section-title">Daftar Belanja</div>

                        <div id="itemsContainer" class="mt-3">
                            <p class="text-muted mb-0">
                                <i class="fas fa-info-circle me-1"></i>
                                Pilih form di atas untuk menampilkan daftar belanja produknya.
                            </p>
                        </div>

                        <!-- ACTION -->
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="submit" id="savePurchaseBtn" class="btn-save" disabled>
                                <i class="fas fa-save me-1"></i>
                                Simpan Pembelian
                            </button>
                        </div>

                    </form>
                </div>

                <div id="editMode" class="panel-mode">
                    <div id="purchase-table"></div>
                </div>
            </div>
        </div>
    </div>    

</div>
</main>

<!-- EDIT MODAL -->
<div class="modal fade" id="editPurchaseModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content stock-panel border-0">

            <div class="panel-header panel-dark my-3 mx-3">
                <div class="panel-left">
                    <div class="panel-icon">
                        <i class="fas fas fa-file-alt"></i>
                    </div>

                    <div>
                        <div class="panel-title">
                            Edit Form Pembelian 
                        </div>
                        <div class="panel-subtitle">
                            Edit informasi pembelian dan detail stok produk
                        </div>
                    </div>
                </div>

                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="mt-2 px-5" id="editPurchaseContent"></div>
        </div>
    </div>
</div>

<?php include '../../script/footscript.php'; ?>

<script>
    // Submit: simpan qty & harga beli ke purchase_items
    document.getElementById("form-stock").addEventListener("submit", function(e){
        e.preventDefault();

        let rows = document.querySelectorAll('#itemsContainer .item-row');
        let valid = true;

        rows.forEach(function(r){
            let qty = r.querySelector('input[name="qty[]"]');
            let price = r.querySelector('input[name="price[]"]');

            if(!qty || !qty.value.trim()){
                QToast('Error', 'Qty wajib diisi', 'error');
                valid = false;
                return;
            }

            if(!price || !price.value.trim()){
                QToast('Error', 'Harga beli wajib diisi', 'error');
                valid = false;
                return;
            }
        });

        if(!valid) return;

        let formData = new FormData(this);

        fetch(this.action,{
            method:"POST",
            body:formData
        })
        .then(res=>res.json())
        .then(res=>{
            if(res.status==="success"){
                QToast('Berhasil', res.msg, 'success');

                $('#purchaseFormSelect option[value="' + res.form_id + '"]').remove();
                $('#purchaseFormSelect').val(null).trigger('change');

                loadPurchaseTable();
            }else{
                QToast("Error", res.msg, "error");
            }
        });
    });

    // Init Select2 form
    function initPurchaseSelect(){
        $('#purchaseFormSelect').select2({
            width: '100%',
            placeholder: 'Pilih Form Daftar Belanja...',
            allowClear: true
        });

        $('#purchaseFormSelect').on('change', function(){
            let id = $(this).val();
            let container = document.getElementById('itemsContainer');

            if(!id){
                container.innerHTML = `
                    <p class="text-muted mb-0">
                        <i class="fas fa-info-circle me-1"></i>
                        Pilih form di atas untuk menampilkan daftar belanja produknya.
                    </p>
                `;
                document.getElementById('savePurchaseBtn').disabled = true;
                return;
            }

            container.innerHTML = `
                <div class="text-center py-5">
                    <i class="fas fa-spinner fa-spin fa-2x text-secondary"></i>
                </div>
            `;

            fetch('purchase-items.php?id=' + id)
            .then(res=>res.text())
            .then(html => {
                container.innerHTML = html;
                document.getElementById('savePurchaseBtn').disabled = false;
            });
        });
    }

    document.addEventListener('DOMContentLoaded', initPurchaseSelect);

    const panelToggle = document.getElementById('panelToggle');

    panelToggle.addEventListener('change', function(){

        const formMode = document.getElementById('formMode');
        const editMode = document.getElementById('editMode');

        const labelForm = document.getElementById('labelForm');
        const labelEdit = document.getElementById('labelEdit');

        if(this.checked){
            formMode.classList.remove('active');
            editMode.classList.add('active');

            labelForm.classList.remove('active');
            labelEdit.classList.add('active');

            loadPurchaseTable();
        }else{
            editMode.classList.remove('active');
            formMode.classList.add('active');

            labelEdit.classList.remove('active');
            labelForm.classList.add('active');
        }

    });

    function loadPurchaseTable(){
        fetch('purchase-table.php')
        .then(res => res.text())
        .then(html => {
            document.getElementById('purchase-table').innerHTML = html;

            setTimeout(() => {

                if ($.fn.DataTable.isDataTable('#purchaseTable')) {
                    $('#purchaseTable').DataTable().destroy();
                }

                let table = $('#purchaseTable').DataTable({
                    pageLength:5,
                    lengthMenu:[[5,10,25,50],[5,10,25,50]],
                    responsive:true,
                    autoWidth:false,
                    order: [[0, 'desc']],
                    ordering: true,
                    language:{
                        search:"",
                        searchPlaceholder:"Cari purchase...",
                        
                        zeroRecords: `
                            <div class="empty-search">
                                <img src="../../assets/img/illustrations/empty-data.png" class="empty-img">
                                <div class="empty-title">Purchase tidak ditemukan</div>
                                <div class="empty-sub">
                                    Coba gunakan kata kunci lain
                                </div>
                            </div>
                        `,

                        emptyTable: `
                            <div class="empty-search">
                                <img src="../../assets/img/illustrations/empty-data.png" class="empty-img">
                                <div class="empty-title">Belum ada data purchase</div>
                                <div class="empty-sub">
                                    Silakan tambahkan purchase terlebih dahulu
                                </div>
                            </div>
                        `
                    }
                });

                table.columns.adjust();

                if(table.responsive){
                    table.responsive.recalc();
                }

            },250);
        });
    }
</script>

<!-- Script Edit -->
<script>
    // OPEN EDIT MODAL
    $(document).on('click','.editPurchaseBtn',function(){

        let id = $(this).data('id');

        $('#editPurchaseModal').modal('show');

        document.getElementById('editPurchaseContent').innerHTML = `
            <div class="text-center py-5">
                <i class="fas fa-spinner fa-spin fa-2x text-secondary"></i>
            </div>
        `;

        fetch('purchase-edit.php?id=' + id)
        .then(res => res.text())
        .then(html => {
            document.getElementById('editPurchaseContent').innerHTML = html;
        });

    });

    // Edit Action
    $(document).on('submit','#editPurchaseForm',function(e){
        e.preventDefault();

        let formData = new FormData(this);

        fetch(this.action,{
            method:'POST',
            body:formData
        })
        .then(res => res.json())
        .then(res => {

            if(res.status === 'success'){

                QToast('Berhasil', res.msg || 'Data berhasil diperbarui', 'success');

                $('#editPurchaseModal').modal('hide');

                loadPurchaseTable();

            }else{

                QToast('Gagal', res.msg || 'Terjadi kesalahan', 'error');

            }

        })
        .catch(() => {
            QToast('Error', 'Gagal memproses update', 'error');
        });
    });
</script>

</body>
</html>