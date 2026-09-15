<?php include '../../sessions/session.php';
include __DIR__ . '/../components/data/stock-status.php';
?>
<table class="table table-hover align-middle" id="stockTable">
    <thead>
        <tr style="font-size:13px;color:#64748b;">
            <th>Produk</th>
            <th class="text-center">Stok</th>
            <th class="text-center">Satuan</th>
            <th class="text-center">Status</th>
        </tr>
    </thead>
    <tbody>

    <?php
    $q = mysqli_query($conn,"
    SELECT
        p.id,
        p.name,
        p.code,
        p.unit,
        COALESCE(SUM(pi.remaining_qty),0) stock
    FROM products p
    LEFT JOIN purchase_items pi
        ON pi.product_id=p.id
        AND pi.deleted_at IS NULL
    WHERE p.category != 'additional'
    GROUP BY p.id
    ORDER BY p.name ASC
    ");
    while($d=mysqli_fetch_assoc($q)): ?>

    <?php
    $stock = (int)$d['stock'];
    $lowStock = resolve_product_low_stock($conn, $d);
    $unit = !empty($d['unit']) ? strtoupper($d['unit']) : '-';
    $status = product_status_view($stock, $lowStock);
    ?>

    <tr class="stock-row"
        onclick="loadDetail(<?= $d['id'] ?>)">

        <td>
            <div class="product-wrap">

                <div class="product-icon">
                    <i class="fas fa-box-open"></i>
                </div>

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

            <span class="st-badge <?= $status['css'] ?>">
                <i class="fas fa-cubes me-1"></i>
                <?= number_format($stock) ?>
            </span>

        </td>

        <td class="text-center">

            <span class="unit-badge">
                <i class="fas fa-balance-scale me-1"></i>
                <?= $unit ?>
            </span>

        </td>

        <td class="text-center">
            <span class="st-badge <?= $status['css'] ?>">
                <i class="fas <?= $status['icon'] ?>"></i>
                <?= $status['label'] ?>
            </span>
        </td>
    </tr>

    <?php endwhile; ?>

    </tbody>
</table>