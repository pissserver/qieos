<?php
    include '../../sessions/session.php';

    $q = mysqli_query($conn, "
        SELECT 
            p.name,
            COALESCE(SUM(pi.remaining_qty), 0) AS stock,
            GROUP_CONCAT(DISTINCT pi.unit SEPARATOR ', ') AS units
        FROM products p
        LEFT JOIN purchase_items pi 
            ON pi.product_id = p.id
        GROUP BY p.id, p.name
        ORDER BY p.name ASC
    ");
    if (!$q) {
    die(mysqli_error($conn));
}
?>

<table id="stockTable" class="table table-striped table-hover align-middle">
    <thead class="table-dark">
        <tr>
            <th style="text-align: center;">Produk</th>
            <th width="180" style="text-align: center;">Total Stok Gudang</th>
            <th width="200" style="text-align: center;">Satuan</th>
        </tr>
    </thead>
    <tbody>
        <?php while($d = mysqli_fetch_assoc($q)): ?>
        <tr>
            <td style="text-align: center;">
                <strong><?= htmlspecialchars($d['name']) ?></strong>
            </td>
            <td style="text-align: center;">
                <span class="badge bg-primary fs-6">
                    <?= (int)$d['stock'] ?>
                </span>
            </td>
            <td style="text-align: center;">
                <span class="badge bg-secondary">
                    <?= htmlspecialchars($d['units']) ?>
                </span>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

