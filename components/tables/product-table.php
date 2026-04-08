<?php
$query = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC");
?>

<div class="card border-0 shadow mt-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="fas fa-box me-2"></i> Data Produk
        </h5>
    </div>

    <div class="card-body">
        <div class="table-responsive" style="overflow-x: auto;">
            <table id="productTable" class="table table-hover align-middle" width="100%">
                <thead class="table-light">
                    <tr>
                        <th style="text-align: center;" width="5%">No</th>
                        <th style="text-align: center;" width="15%">Foto</th>
                        <th style="text-align: center;" width="20%">Produk</th>
                        <th style="text-align: center;" width="20%">Kategory</th>
                        <th style="text-align: center;" width="20%">Harga</th>
                        <th style="text-align: center;" width="20%">Action</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($query)) {
                    ?>
                        <tr>
                            <td style="text-align: center;"><?php echo $no++; ?></td>
                            <td style="text-align: center;">
                                <img
                                    src="../assets/img/uploads/<?php echo $row['photo']; ?>"
                                    style="width:60px;height:60px;object-fit:cover;border-radius:8px;">
                            </td>
                            <td>
                                <b><?php echo ucwords(strtolower($row['product_name'])); ?></b>
                            </td>
                            <td style="text-align: center;">
                                <span class="badge bg-info p-2">
                                    <i class="fas fa-box"></i>&nbsp;&nbsp;<?php echo ucfirst($row['category']); ?>
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <span class="badge bg-success p-2">
                                    <i class="fas fa-money-bill-wave"></i>&nbsp;&nbsp;Rp <?php echo number_format($row['price']); ?>
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <a href="product-edit.php?id=<?php echo $row['id']; ?>"
                                    class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>&nbsp;Edit
                                </a>

                                <button
                                    class="btn btn-sm btn-danger"
                                    onclick="deleteProduct(<?php echo $row['id']; ?>)">
                                    <i class="fas fa-trash"></i>&nbsp;Hapus
                                </button>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<script>
    $(document).ready(function() {
        $('#productTable').DataTable({
            pageLength: 5,
            lengthChange: false,
            language: {
                search: "Cari Produk :",
                paginate: {
                    previous: "Previous",
                    next: "Next"
                }
            }
        });
    });
</script>

<script>
    function deleteProduct(id) {
        Swal.fire({
            title: 'Yakin hapus?',
            text: "Data produk akan dihapus!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya Hapus'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location = "product-action.php?action=delete&id=" + id;
            }
        });
    }
</script>