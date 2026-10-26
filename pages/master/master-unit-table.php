<?php
include '../../sessions/session.php';
?>
<table class="table table-hover align-middle" id="stockTable">
    <thead>
        <tr style="font-size:13px;color:#64748b;">
            <th>Nama Unit</th>
            <th class="text-center">Dipakai</th>
            <th class="text-center">Catatan</th>
            <th class="text-center">Aksi</th>
        </tr>
    </thead>
    <tbody>
    <?php
    $q = mysqli_query($conn,"
        SELECT u.*,
               (SELECT COUNT(*) FROM products p WHERE p.unit = u.name AND p.deleted_at IS NULL) AS used_count
        FROM units u
        WHERE u.deleted_at IS NULL
        ORDER BY u.name ASC
    ");
    while($d=mysqli_fetch_assoc($q)): ?>
    <tr class="stock-row">
        <td>
            <div class="product-wrap">
                <div class="product-img-placeholder">
                    <i class="fas fa-ruler-combined"></i>
                </div>
                <div>
                    <div class="fw-bold text-capitalize"><?= htmlspecialchars($d['name']) ?></div>
                    <div class="mp-meta mp-meta-col">
                        <small class="text-muted">
                            <i class="fas fa-tag me-1"></i><?= htmlspecialchars($d['note'] ?: '-') ?>
                        </small>
                    </div>
                </div>
            </div>
        </td>
        <td class="text-center mp-col-phone">
            <span class="unit-badge"><?= (int)$d['used_count'] ?> produk</span>
        </td>
        <td class="text-center mp-col-address">
            <span class="stock-badge <?= $d['note'] ? 'stock-success' : 'stock-empty' ?>"><?= htmlspecialchars($d['note'] ?: '-') ?></span>
        </td>
        <td class="text-center">
            <button class="action-btn btn-edit editUnitBtn" data-id="<?= $d['id'] ?>"><i class="fas fa-edit"></i></button>
            <button class="action-btn btn-delete deleteUnitBtn" data-id="<?= $d['id'] ?>" data-name="<?= htmlspecialchars($d['name']) ?>"><i class="fas fa-trash"></i></button>
        </td>
    </tr>
    <?php endwhile; ?>
    </tbody>
</table>
