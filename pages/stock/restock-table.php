<?php
include '../../sessions/session.php';

$q = mysqli_query($conn,"
    SELECT
        p.id,
        p.form,
        p.receipt,
        p.date,
        COUNT(pi.id) AS total_items,
        COALESCE(SUM(pi.price_buy), 0) AS total_price,
        MAX(CASE WHEN pi.qty_stock IS NOT NULL THEN 1 ELSE 0 END) AS used_flag
    FROM purchases p
    LEFT JOIN purchase_items pi
        ON p.id = pi.purchase_id
        AND pi.deleted_at IS NULL
    WHERE p.deleted_at IS NULL
      AND pi.qty_buy IS NOT NULL
    GROUP BY p.id, p.form, p.receipt, p.date
    HAVING COUNT(pi.id) > 0
    ORDER BY p.date DESC, p.id DESC
");
?>

<!-- Catatan: CSS restock-table.css dimuat di <head> restock.php,
     bukan di sini. Kalau <link> ada di fragment ini, tiap reload innerHTML
     memicu fetch + parse ulang stylesheet dan tabel kedap-kedip tanpa style
     sebentar (glitch CSS saat toggle). -->

<table id="purchaseTable" class="table table-hover align-middle">
<thead>
<tr>
    <th>ID FORM</th>
    <th class="text-center">TANGGAL PEMBUATAN</th>
    <th class="text-center">TOTAL ITEM</th>
    <th class="text-center">TOTAL BELANJA</th>
    <th class="text-center">AKSI</th>
</tr>
</thead>

<tbody>
<?php
    while($d=mysqli_fetch_assoc($q)): 
?>
<tr class="purchase-row<?= (int)$d['used_flag'] === 1 ? ' is-restocked' : '' ?><?= !empty($d['receipt']) ? ' has-receipt' : '' ?>" data-id="<?= $d['id'] ?>" data-date="<?= htmlspecialchars($d['date'], ENT_QUOTES, 'UTF-8') ?>">

    <!-- ID -->
    <td>
        <div class="purchase-box">
            <div class="purchase-icon">
                <i class="fas fa-file-invoice"></i>
            </div>

            <div class="purchase-box-text">
                <div class="fw-bold"><?= htmlspecialchars($d['form']) ?></div>
                <?php if(!empty($d['receipt'])): ?>
                <span class="purchase-receipt-badge table-receipt-badge mt-1">
                    <i class="fas fa-circle-check"></i>
                    <span><small>RECEIPT</small><strong><?= htmlspecialchars($d['receipt']) ?></strong></span>
                </span>
                <?php else: ?>
                <small class="text-muted">
                    <i class="fas fa-receipt me-1"></i>Daftar Belanja
                </small>
                <?php endif; ?>

                <!-- ringkasan tambahan, hanya tampil di mobile -->
                <div class="mp-meta">
                    <span class="mp-meta-item mp-meta-item-date">
                        <i class="fas fa-calendar-alt"></i>
                        <?= date('d M Y', strtotime($d['date'])) ?>
                    </span>
                    <span class="mp-meta-item">
                        <i class="fas fa-box"></i>
                        <?= $d['total_items'] ?> item
                    </span>

                    <?php if((int)$d['used_flag'] === 1): ?>
                    <span class="mp-meta-item mp-meta-chip-restocked">
                        <i class="fas fa-circle-check"></i>
                        Stok masuk
                    </span>
                    <?php endif; ?>

                    <span class="mp-meta-total">
                        <span class="mp-meta-total-value">
                            Rp <?= number_format($d['total_price'], 0, ',', '.') ?>
                        </span>
                    </span>
                </div>
            </div>
        </div>
    </td>

    <!-- DATE -->
    <td class="text-center mp-hide">
        <span class="date-badge">
            <i class="fas fa-calendar-alt"></i>
            <?= date('d F Y', strtotime($d['date'])) ?>
        </span>
    </td>

    <!-- TOTAL ITEM -->
    <td class="text-center mp-hide">
        <span class="note-badge">
            <i class="fas fa-box"></i>
            <?= $d['total_items'] ?>
        </span>
    </td>

    <!-- TOTAL PRICE -->
    <td class="text-center mp-hide">
        <span class="created-badge">
            <i class="fas fa-money-bill"></i>
            Rp <?= number_format($d['total_price'], 0, ',', '.') ?>
        </span>
    </td>

    <!-- ACTION -->
    <td class="text-center">
        <div class="mp-actions">

            <?php if(empty($d['receipt']) && (int)$d['used_flag'] === 0): ?>
            <button class="action-btn btn-delete deletePurchaseBtn"
                data-id="<?= $d['id'] ?>"
                title="Hapus daftar belanja"
                data-date="<?= $d['date'] ?>">
                <i class="fas fa-trash"></i>
            </button>
            <?php endif; ?>

            <?php if(empty($d['receipt'])): ?>
            <button class="action-btn btn-print printPurchaseBtn"
                data-id="<?= $d['id'] ?>"
                title="Print daftar belanja"
                data-date="<?= $d['date'] ?>">
                <i class="fas fa-print"></i>
            </button>
            <?php endif; ?>

            <button class="action-btn btn-edit editPurchaseBtn" data-id="<?= $d['id'] ?>" title="Edit daftar belanja">
                <i class="fas fa-edit"></i>
            </button>

            <?php if(!empty($d['receipt']) && (int)$d['used_flag'] === 0): ?>
            <button class="action-btn btn-restock restockPurchaseBtn"
                data-id="<?= $d['id'] ?>"
                data-date="<?= $d['date'] ?>"
                title="Validasi & Restock ke stok gudang">
                <i class="fas fa-truck-ramp-box"></i>
            </button>
            <?php elseif(!empty($d['receipt'])): ?>
            <button class="action-btn btn-restocked restockPurchaseBtn"
                data-id="<?= $d['id'] ?>"
                data-date="<?= $d['date'] ?>"
                title="Sudah direstock ke stok gudang">
                <i class="fas fa-circle-check"></i>
            </button>
            <?php endif; ?>

        </div>
    </td>

</tr>
<?php endwhile; ?>
</tbody>
</table>