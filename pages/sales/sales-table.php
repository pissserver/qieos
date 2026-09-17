<?php
    include '../../sessions/session.php';

    $q = mysqli_query($conn,"
    SELECT
        p.id,
        p.name,
        p.code,
        p.unit,
        p.photo,
        p.catalog,
        COALESCE(SUM(s.qty),0) AS qty
    FROM sales_stock s
    JOIN products p
        ON p.id = s.product_id
    GROUP BY p.id
    ORDER BY p.name ASC
    ");
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/stock.css">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/sales-table.css">

<table class="table table-hover align-middle" id="stockTable">
    <thead>
        <tr style="font-size:13px;color:#64748b;">
            <th>Produk</th>
            <th class="text-center">Stok</th>
            <th class="text-center">Status</th>
            <th class="text-center">Katalog</th>
        </tr>
    </thead>
    <tbody>

    <?php while($d=mysqli_fetch_assoc($q)): ?>

    <?php
        $stock = (int)$d['qty'];

        if($stock <= 0){
            $statusText='Habis';
            $statusClass='st-habis';
            $statusIcon='fa-circle-xmark';
        }elseif($stock <= 50){
            $statusText='Menipis';
            $statusClass='st-menipis';
            $statusIcon='fa-triangle-exclamation';
        }else{
            $statusText='Ready';
            $statusClass='st-ready';
            $statusIcon='fa-circle-check';
        }

        $unit = !empty($d['unit']) ? strtoupper($d['unit']) : '-';
        $isActive = ($d['catalog'] === 'active');
    ?>

    <tr class="stock-row <?= $isActive ? 'catalog-active' : '' ?>" id="row-<?= $d['id'] ?>">
        <td>
            <div class="product-wrap">
                <?php if(!empty($d['photo'])): ?>
                <img class="product-img"
                    src="<?= BASE_URL ?>/assets/img/products/<?= htmlspecialchars($d['photo']) ?>"
                    alt="<?= htmlspecialchars($d['name']) ?>">
                <?php else: ?>
                <div class="product-icon">
                    <i class="fas fa-box-open"></i>
                </div>
                <?php endif; ?>

                <div>
                    <div class="fw-bold">
                        <?= htmlspecialchars($d['name']) ?>
                    </div>
                    <small class="text-muted">
                        <?= htmlspecialchars($d['code']) ?>
                    </small>
                </div>
            </div>
        </td>

        <td class="text-center">
            <span class="st-badge <?= $statusClass ?>">
                <i class="fas fa-cubes me-1"></i>
                <?= number_format($stock) ?><?= $unit !== '-' ? ' ' . $unit : '' ?>
            </span>
        </td>

        <td class="text-center">
            <span class="st-badge <?= $statusClass ?>">
                <i class="fas <?= $statusIcon ?>"></i>
                <?= $statusText ?>
            </span>
        </td>

        <td class="text-center">
            <label class="toggle-switch" title="Klik untuk ubah status katalog">
                <input
                    type="checkbox"
                    <?= $isActive ? 'checked' : '' ?>
                    onchange="toggleCatalog(<?= $d['id'] ?>, this)">
                <span class="toggle-track">
                    <span class="toggle-thumb"></span>
                </span>
            </label>
        </td>
    </tr>

    <?php endwhile; ?>

    </tbody>
</table>