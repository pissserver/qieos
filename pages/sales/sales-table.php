<?php
    include '../../sessions/session.php';
    include __DIR__ . '/../components/data/stock-status.php';

    $q = mysqli_query($conn,"
    SELECT
        p.id,
        p.name,
        p.code,
        p.unit,
        p.photo,
        p.catalog,
        p.low_stock,
        p.low_stock_kantin,
        COALESCE(SUM(s.qty),0) AS qty
    FROM sales_stock s
    JOIN products p
        ON p.id = s.product_id
    GROUP BY p.id
    ORDER BY p.name ASC
    ");
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/stock.css?v=<?php echo filemtime(__DIR__ . '/../../css/pages/stock.css'); ?>">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/sales-table.css?v=<?php echo filemtime(__DIR__ . '/../../css/pages/sales-table.css'); ?>">

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
        $lowStock = resolve_product_low_stock_kantin($conn, $d);
        $status = product_status_view($stock, $lowStock);

        $statusText = $status['label'];
        $statusClass = $status['css'];
        $statusIcon = $status['icon'];
        $statusKey = $status['key'];

        $unit = !empty($d['unit']) ? strtoupper($d['unit']) : '-';
        $isActive = ($d['catalog'] === 'active');
    ?>

    <tr class="stock-row row-st-<?= $statusKey ?> <?= $isActive ? 'catalog-active' : '' ?>" id="row-<?= $d['id'] ?>">
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

                    <div class="product-meta">
                        <small class="text-muted">
                            <?= htmlspecialchars($d['code']) ?>
                        </small>

                        <span class="st-badge <?= $statusClass ?> st-inline">
                            <i class="fas <?= $statusIcon ?>"></i>
                            <?= $statusText ?>
                        </span>
                    </div>
                </div>
            </div>
        </td>

        <td class="text-center">
            <span class="st-badge <?= $statusClass ?>">
                <i class="fas fa-cubes me-1"></i>
                <b class="stock-qty"><?= number_format($stock) ?></b>
                <?php if($unit !== '-'): ?>
                <span class="stock-unit"><?= htmlspecialchars($unit) ?></span>
                <?php endif; ?>
            </span>
        </td>

        <td class="text-center st-cell-status">
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