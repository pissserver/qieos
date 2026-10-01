<?php
include '../../sessions/session.php';
?>
<table class="table table-hover align-middle" id="stockTable">
    <thead>
        <tr style="font-size:13px;color:#64748b;">
            <th>Nama Supplier</th>
            <th class="text-center">Telepon</th>
            <th class="text-center">Alamat</th>
            <th class="text-center">Aksi</th>
        </tr>
    </thead>
    <tbody>
    <?php
    $q = mysqli_query($conn,"SELECT * FROM suppliers WHERE deleted_at IS NULL ORDER BY name ASC");
    while($d=mysqli_fetch_assoc($q)): ?>
    <tr class="stock-row">
        <td>
            <div class="product-wrap">
                <div class="product-img-placeholder">
                    <i class="fas fa-truck"></i>
                </div>
                <div>
                    <div class="fw-bold"><?= htmlspecialchars($d['name']) ?></div>
                    <div class="mp-meta">
                        <small class="text-muted"><?= htmlspecialchars($d['phone'] ?: '-') ?></small>
                        <small class="text-muted mp-meta-sep"><?= htmlspecialchars($d['address'] ?: '-') ?></small>
                    </div>
                </div>
            </div>
        </td>
        <td class="text-center mp-col-phone">
            <span class="unit-badge"><?= htmlspecialchars($d['phone'] ?: '-') ?></span>
        </td>
        <td class="text-center mp-col-address">
            <span class="stock-badge stock-success"><?= htmlspecialchars($d['address'] ?: '-') ?></span>
        </td>
        <td class="text-center">
            <button class="action-btn btn-edit editSupplierBtn" data-id="<?= $d['id'] ?>"><i class="fas fa-edit"></i></button>
            <button class="action-btn btn-delete deleteSupplierBtn" data-id="<?= $d['id'] ?>" data-name="<?= htmlspecialchars($d['name']) ?>"><i class="fas fa-trash"></i></button>
        </td>
    </tr>
    <?php endwhile; ?>
    </tbody>
</table>