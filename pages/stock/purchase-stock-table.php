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
GROUP BY p.id;
");
?>

<style>
    /* DATATABLE TOP */

    #stockTable{
        border-collapse:separate;
        border-spacing:0 14px;
    }

    #stockTable thead th{
        font-size:12px;
        color:#64748b;
        font-weight:700;
        border:none !important;
        text-transform:uppercase;
    }

    /* ROW CARD */
    .stock-row{
        background:#fff;
        border-radius:16px;
        box-shadow:0 6px 18px rgba(15,23,42,.05);
        transition:.25s;
    }

    .stock-row:hover{
        transform:translateY(-3px);
        box-shadow:0 12px 24px rgba(15,23,42,.10);
    }

    .stock-row td:first-child{
        border-radius:14px 0 0 14px;
    }   

    .stock-row td:last-child{
        border-radius:0 14px 14px 0;
    }

    #stockTable tbody td{
        padding:20px 18px;
        border:none !important;
        vertical-align:middle;
    }

    /* PRODUCT */
    .stock-product{
        display:flex;
        align-items:center;
        gap:14px;
    }

    .product-icon{
        width:48px;
        height:48px;
        border-radius:14px;
        background:linear-gradient(135deg,#334155,#0f172a);
        display:flex;
        align-items:center;
        justify-content:center;
        color:#fff;
        font-size:18px;
    }

    /* BADGES */
    .category-badge{
        display:inline-flex;
        align-items:center;
        gap:8px;
        background:#eef2ff;
        color:#4338ca;
        padding:8px 14px;
        border-radius:10px;
        font-weight:600;
    }

    .stock-badge{
        display:inline-flex;
        align-items:center;
        gap:8px;
        background:#e2e8f0;
        color:#0f172a;
        padding:8px 14px;
        border-radius:10px;
        font-weight:700;
    }

    .stock-success{
        background:#dcfce7 !important;
        color:#166534 !important;
        border:1px solid #86efac;
    }

    .stock-danger{
        background:#fee2e2 !important;
        color:#991b1b !important;
        border:1px solid #fca5a5;
    }

    .unit-badge{
        display:inline-flex;
        align-items:center;
        gap:8px;
        background:#f8fafc;
        border:1px solid #e2e8f0;
        color:#334155;
        padding:8px 14px;
        border-radius:10px;
        font-weight:600;
    }
</style>

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
