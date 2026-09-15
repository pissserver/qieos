<?php
include '../../sessions/session.php';
?>
<table class="table table-hover align-middle" id="stockTable">
    <thead>
        <tr style="font-size:13px;color:#64748b;">
            <th>Produk</th>
            <th class="text-center">Kode</th>
            <th class="text-center">Kategori</th>
            <th class="text-center">Harga Jual</th>
            <th class="text-center">Aksi</th>
        </tr>
    </thead>
    <tbody>

    <?php
    $q = mysqli_query($conn,"
    SELECT * FROM products
    WHERE deleted_at IS NULL
    ORDER BY name ASC
    ");
    while($d=mysqli_fetch_assoc($q)):
    ?>

    <tr class="stock-row">

        <td>
            <div class="product-wrap">

                <?php if(!empty($d['photo'])): ?>
                    <img class="product-img"
                        src="<?php echo BASE_URL; ?>/assets/img/products/<?= htmlspecialchars($d['photo']) ?>"
                        alt="<?= htmlspecialchars($d['name']) ?>">
                <?php else: ?>
                    <div class="product-img-placeholder">
                        <i class="fas fa-box"></i>
                    </div>
                <?php endif; ?>

                <div>
                    <div class="fw-bold">
                        <?= htmlspecialchars($d['name']) ?>
                    </div>
                </div>

            </div>
        </td>

        <td class="text-center">
            <span class="unit-badge">
                <?= htmlspecialchars($d['code']) ?>
            </span>
        </td>

        <td class="text-center">
            <span class="stock-badge stock-success">
                <?= ucwords(strtolower(htmlspecialchars($d['category']))) ?>
            </span>
        </td>

        <td class="text-center">
            <span class="price-badge">
                Rp <?= number_format($d['sell_price'], 0, ',', '.') ?>
            </span>
        </td>

        <td class="text-center">
            <a href="<?php echo BASE_URL; ?>/pages/master/master-product-detail.php?id=<?= $d['id'] ?>"
               class="action-btn btn-view"
               title="Lihat Detail">
                <i class="fas fa-eye"></i>
            </a>
        </td>
    </tr>

    <?php endwhile; ?>

    </tbody>
</table>