<?php
include '../../sessions/session.php';
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Stok Gudang - Qieos</title>
    <?php include '../../script/headscript.php'; ?>

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/stock.css">
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

    <div class="section-card mb-4 mt-5">
        <div class="panel-header panel-primary">
            <div class="panel-left">
                <div class="panel-icon">
                    <i class="fas fa-boxes-stacked"></i>
                </div>

                <div>
                    <div class="panel-title">
                        Stok Gudang
                    </div>
                    <div class="panel-subtitle">
                        List stok produk siap transfer ke penjualan
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 px-4">
            <!-- TABLE -->
            <div class="table-responsive-wrap" id="stockTableContainer">
                <!-- Loaded via AJAX -->
            </div>
        </div>
    </div>

    <!-- EDIT MODAL -->
    <div class="modal fade" id="editStockModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content stock-panel border-0">

                <div class="panel-header panel-dark my-3 mx-3">
                    <div class="panel-left">
                        <div class="panel-icon">
                            <i class="fas fas fa-box-open"></i>
                        </div>

                        <div>
                            <div class="panel-title">
                                Edit Stock Gudang 
                            </div>
                            <div class="panel-subtitle">
                                Edit nama, harga jual, dan foto produk
                            </div>
                        </div>
                    </div>

                    <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="mt-2 px-5" id="editStockContent"></div>
            </div>
        </div>
    </div>

    <!-- DETAIL FIFO -->
    <div class="fifo-container mt-4 mb-5">

        <div class="fifo-title">
            <i class="fas fa-layer-group text-primary"></i>
            <h5 class="mb-0">Layer FIFO</h5>
        </div>

        <div id="fifo-detail">

            <div class="text-center py-4">

                <i class="fas fa-box-open fa-3x text-secondary mb-3"></i>

                <p class="text-muted mb-0">
                    Klik salah satu produk untuk melihat detail FIFO
                </p>

            </div>

        </div>

    </div>

</div>
</main>

<?php include '../../script/footscript.php'; ?>

<script>
    function loadStockTable(){
        fetch('stock-table.php')
        .then(res=>res.text())
        .then(html=>{
            document.getElementById('stockTableContainer').innerHTML = html;

            if($.fn.DataTable.isDataTable('#stockTable')){
                $('#stockTable').DataTable().destroy();
            }

            setTimeout(()=>{
            $('#stockTable').DataTable({
                pageLength: 5,
                lengthMenu:[[5,10,25,50],[5,10,25,50]],
                responsive: true,
                autoWidth: false,
                language:{
                    search:"",
                    searchPlaceholder:"Cari produk...",
                    zeroRecords: `
                        <div class="empty-search">
                            <img src="../../assets/img/illustrations/empty-data.png" class="empty-img">
                            <div class="empty-title">Produk tidak ditemukan</div>
                            <div class="empty-sub">Coba gunakan kata kunci lain</div>
                        </div>
                    `,
                    emptyTable: `
                        <div class="empty-search">
                            <img src="../../assets/img/illustrations/empty-data.png" class="empty-img">
                            <div class="empty-title">Belum ada data produk</div>
                            <div class="empty-sub">Silakan tambahkan stok terlebih dahulu</div>
                        </div>
                    `
                }
            });
            },100);
        });
    }

    $(document).ready(function(){
        loadStockTable();
    });

    function loadDetail(id){

        fetch('stock-detail.php?id=' + id)
        .then(res => res.text())
        .then(html => {

            document.getElementById("fifo-detail").innerHTML = html;

            if ($.fn.DataTable.isDataTable('#tableStock')) {
                $('#tableStock').DataTable().destroy();
            }

            $('#tableStock').DataTable({
                pageLength: 10,
                searching: true,
                responsive: true,
                autoWidth: false,
                ordering: false,
                language: {
                    search: "",
                    searchPlaceholder:"Cari detail...",
                    zeroRecords: "Data tidak ditemukan",
                    paginate: {
                        first: "Awal",
                        last: "Akhir",
                        next: "›",
                        previous: "‹"
                    }
                }
            });

        });

    }
</script>

<!-- Script Edit -->
<script>
    // OPEN EDIT MODAL
    $(document).on('click','.editStockBtn',function(){

        let id = $(this).data('id');

        $('#editStockModal').modal('show');

        document.getElementById('editStockContent').innerHTML = `
            <div class="text-center py-5">
                <i class="fas fa-spinner fa-spin fa-2x text-secondary"></i>
            </div>
        `;

        fetch('stock-edit.php?id=' + id)
        .then(res => res.text())
        .then(html => {
            document.getElementById('editStockContent').innerHTML = html;
        });

    });

    // Edit Action
    $(document).on('submit','#editStockForm',function(e){
        e.preventDefault();

        let formData = new FormData(this);
        let id = formData.get('id');

        fetch('stock-action.php?action=update&id='+id,{
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

                $('#editStockModal').modal('hide');

                loadStockTable();

            }else{

                QToast({
                    title:'Gagal',
                    message:res.msg || 'Terjadi kesalahan',
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

</body>
</html>