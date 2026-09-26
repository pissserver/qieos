<?php include '../../sessions/session.php'; ?>
<table class="table table-hover align-middle" id="stockTable">
    <thead>
        <tr style="font-size:13px;color:#64748b;">
            <th>Nama Customer</th>
            <th class="text-center">No. Telepon</th>
            <th class="text-center">Aksi</th>
        </tr>
    </thead>
    <tbody>
    <?php
    $q = mysqli_query($conn,"SELECT * FROM customers WHERE deleted_at IS NULL ORDER BY name ASC");
    while($d=mysqli_fetch_assoc($q)): ?>
    <tr class="stock-row">
        <td>
            <div class="product-wrap">
                <div class="product-img-placeholder" style="background:linear-gradient(135deg,#0284c7,#0ea5e9);">
                    <i class="fas fa-user"></i>
                </div>
                <div>
                    <div class="fw-bold"><?= htmlspecialchars($d['name']) ?></div>
                </div>
            </div>
        </td>
        <td class="text-center">
            <span class="unit-badge"><?= htmlspecialchars($d['phone'] ?: '-') ?></span>
        </td>
        <td class="text-center">
            <button class="action-btn btn-edit editCustomerBtn" data-id="<?= $d['id'] ?>"><i class="fas fa-edit"></i></button>
            <button class="action-btn btn-delete deleteCustomerBtn" data-id="<?= $d['id'] ?>" data-name="<?= htmlspecialchars($d['name']) ?>"><i class="fas fa-trash"></i></button>
        </td>
    </tr>
    <?php endwhile; ?>
    </tbody>
</table>