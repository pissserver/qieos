<?php
include '../../sessions/session.php';

$q = mysqli_query($conn,"
SELECT
    s.qty,
    p.name,
    p.code
FROM sales_stock s
JOIN products p
    ON p.id = s.product_id
ORDER BY p.name ASC
");
?>

<style>
    #salesTable{
        border-collapse:separate;
        border-spacing:0 14px;
    }

    #salesTable thead th{
        border:none !important;
        color:#64748b;
        font-size:12px;
        text-transform:uppercase;
        font-weight:700;
    }

    /* ROW CARD */

    .sales-row{
        background:#fff;
        border-radius:16px;
        box-shadow:0 6px 18px rgba(15,23,42,.05);
        transition:.25s;
    }

    .sales-row:hover{
        box-shadow:0 12px 24px rgba(15,23,42,.10);
        transform:translateY(-2px);
    }

    .sales-row td{
        padding:20px 18px;
        border:none !important;
        vertical-align:middle;
    }

    .sales-row:hover td{
        box-shadow:0 14px 28px rgba(15,23,42,.10);
    }

    .sales-row td:first-child{
        border-radius:16px 0 0 16px;
    }

    .sales-row td:last-child{
        border-radius:0 16px 16px 0;
    }

    /* PRODUCT */

    .product-wrap{
        display:flex;
        align-items:center;
        gap:15px;
    }

    .product-icon{
        width:50px;
        height:50px;
        border-radius:14px;
        background:linear-gradient(135deg,#334155,#0f172a);
        display:flex;
        align-items:center;
        justify-content:center;
        color:#fff;
        font-size:18px;
        flex-shrink:0;
    }

    .product-name{
        font-weight:700;
        color:#0f172a;
    }

    .product-code{
        color:#64748b;
        font-size:12px;
        margin-top:2px;
    }

    /* BADGES */

    .stock-badge{
        display:inline-flex;
        align-items:center;
        gap:8px;
        padding:8px 14px;
        border-radius:10px;
        font-weight:700;
    }

    .stock-success{
        background:#dcfce7;
        color:#166534;
    }

    .stock-low{
        background:#fef3c7;
        color:#92400e;
    }

    .stock-empty{
        background:#fee2e2;
        color:#991b1b;
    }
</style>

<table id="salesTable" class="table table-hover align-middle">

<thead>
<tr>
    <th>Produk</th>
    <th class="text-center">Stok</th>
    <th class="text-center">Status</th>
</tr>
</thead>

<tbody>

<?php while($d=mysqli_fetch_assoc($q)): ?>

<?php

$stock = (int)$d['qty'];

if($stock <= 0){

    $statusText  = 'Habis';
    $statusClass = 'stock-empty';
    $statusIcon  = 'fa-circle-xmark';

}elseif($stock <= 50){

    $statusText  = 'Menipis';
    $statusClass = 'stock-low';
    $statusIcon  = 'fa-triangle-exclamation';

}else{

    $statusText  = 'Aman';
    $statusClass = 'stock-success';
    $statusIcon  = 'fa-circle-check';

}

?>

<tr class="sales-row">

    <td>

        <div class="product-wrap">

            <div class="product-icon">
                <i class="fas fa-box-open"></i>
            </div>

            <div>

                <div class="product-name">
                    <?= htmlspecialchars($d['name']) ?>
                </div>

                <div class="product-code">
                    <?= htmlspecialchars($d['code']) ?>
                </div>

            </div>

        </div>

    </td>

    <td class="text-center">

        <span class="stock-badge <?= $statusClass ?>">

            <i class="fas fa-cubes"></i>

            <?= number_format($stock) ?>

        </span>

    </td>

    <td class="text-center">

        <span class="stock-badge <?= $statusClass ?>">

            <i class="fas <?= $statusIcon ?>"></i>

            <?= $statusText ?>

        </span>

    </td>

</tr>

<?php endwhile; ?>

</tbody>
</table>