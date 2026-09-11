<?php
include '../../sessions/session.php';

$q = mysqli_query($conn,"
SELECT 
    p.name,
    p.code,
    p.category,
    COALESCE(SUM(pi.remaining_qty),0) AS stock,
    GROUP_CONCAT(DISTINCT pi.unit) AS units
FROM products p
LEFT JOIN purchase_items pi 
    ON pi.product_id = p.id
    AND pi.deleted_at IS NULL
WHERE p.category != 'additional'
GROUP BY p.id;
");
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/purchase-stock-table.css">

<table id="stockTable" class="table table-hover align-middle">
<thead>
<tr>
    <th>PRODUK</th>
    <th class="text-center">KATEGORI</th>
    <th class="text-center">STOK</th>
    <th class="text-center">SATUAN</th>
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
                <div class="fw-bold"><?= $d['name'] ?> (<?= $d['code'] ?>)</div>
                <small class="text-muted">
                    <i class="fas fa-warehouse me-1"></i>Gudang Stok
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

    <!-- STOK -->
    <td class="text-center">
        <?php 
            $stockClass = ($d['stock'] < 10) ? 'stock-danger' : 'stock-success';
        ?>
        
        <span class="stock-badge <?= $stockClass ?>">
            <i class="fas fa-cubes"></i>
            <?= $d['stock'] ?>
        </span>
    </td>

    <!-- SATUAN -->
    <td class="text-center">
        <span class="unit-badge text-uppercase">
            <i class="fas fa-balance-scale"></i>
            <?= $d['units'] ?>
        </span>
    </td>

</tr>
<?php endwhile; ?>
</tbody>
</table>
