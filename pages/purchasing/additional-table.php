<?php
include '../../sessions/session.php';

$q = mysqli_query($conn,"
SELECT 
    *
FROM products
WHERE category = 'additional'
ORDER BY name ASC;
");
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/additional-table.css">

<table id="stockTable" class="table table-hover align-middle">
<thead>
<tr>
    <th>PRODUK</th>
    <th class="text-center">KATEGORI</th>
    <th class="text-center">HARGA JUAL</th>
    <th class="text-center">AKSI</th>
</tr>
</thead>

<tbody>
<?php while($d=mysqli_fetch_assoc($q)): ?>
<tr class="stock-row">

    <!-- PRODUK -->
    <td>
        <div class="stock-product">
            <div class="product-icon">
                <i class="fas fa-box-open"></i>
            </div>

            <div>
                <div class="fw-bold"><?= $d['name'] ?></div>
                <small class="text-muted">
                    <i class="fas fa-tags me-1"></i>Produk Tambahan
                </small>
            </div>
        </div>
    </td>

    <!-- KATEGORI -->
    <td class="text-center">
        <span class="category-badge">
            <i class="fas fa-tags"></i>
            <?= ucfirst($d['category']) ?>
        </span>
    </td>

    <td class="text-center">
        <span class="stock-badge stock-success">
            <i class="fas fa-money-bill-wave"></i>
            Rp <?= number_format($d['sell_price'],0,',','.') ?>
        </span>
    </td>

    <td class="text-center">
        <button class="action-btn btn-edit editAdditionalBtn" data-id="<?= $d['id'] ?>">
            <i class="fas fa-edit"></i>
        </button>

        <button class="action-btn btn-delete deleteAdditionalBtn"
            data-id="<?= $d['id'] ?>"
            data-name="<?= $d['name'] ?>">
                <i class="fas fa-trash"></i>
        </button>
    </td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
