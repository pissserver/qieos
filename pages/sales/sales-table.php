<?php
    include '../../sessions/session.php';

    $q = mysqli_query($conn,"
    SELECT
        s.qty,
        p.id,
        p.name,
        p.code,
        p.catalog
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
        width:100%;
    }

    /* HEADER */
    #salesTable thead th{
        border:none !important;
        color:#64748b;
        font-size:12px;
        text-transform:uppercase;
        font-weight:700;
    }

    /* ROW */
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
        padding:18px;
        border:none !important;
        vertical-align:middle;
    }

    /* PRODUCT */
    .product-wrap{
        display:flex;
        align-items:center;
        gap:12px;
    }

    .product-icon{
        width:46px;
        height:46px;
        border-radius:14px;
        background:linear-gradient(135deg,#334155,#0f172a);
        display:flex;
        align-items:center;
        justify-content:center;
        color:#fff;
    }

    .product-name{
        font-weight:700;
        color:#0f172a;
    }

    .product-code{
        font-size:12px;
        color:#64748b;
    }

    /* STOCK BADGE */
    .stock-badge{
        display:inline-flex;
        align-items:center;
        gap:6px;
        padding:6px 12px;
        border-radius:10px;
        font-weight:700;
        font-size:12px;
    }

    .stock-success{background:#dcfce7;color:#166534;}
    .stock-low{background:#fef3c7;color:#92400e;}
    .stock-empty{background:#fee2e2;color:#991b1b;}

    /* ===========================
    NEUMORPHIC PREMIUM SWITCH
    =========================== */

    .neo-switch{
        position:relative;
        display:inline-block;
        width:92px;
        height:46px;
        cursor:pointer;
    }

    .neo-switch input{
        display:none;
    }

    /* TRACK */

    .neo-track{

        position:absolute;
        inset:0;

        border-radius:999px;

        background:#eef2f6;

        box-shadow:
            inset 6px 6px 12px rgba(209,214,230,.8),
            inset -6px -6px 12px rgba(255,255,255,.95);

        transition:.35s ease;

    }

    /* THUMB */

    .neo-thumb{

        position:absolute;

        left:5px;
        top:5px;

        width:36px;
        height:36px;

        border-radius:50%;

        background:#ffffff;

        display:flex;
        justify-content:center;
        align-items:center;

        box-shadow:
            4px 4px 10px rgba(209,214,230,.9),
            -4px -4px 10px rgba(255,255,255,.95);

        transition:all .35s cubic-bezier(.4,0,.2,1);

    }

    /* TEXT */

    .neo-text{

        font-size:11px;
        font-weight:700;
        letter-spacing:.5px;
        color:#64748b;
        transition:.3s;

    }

    /* ACTIVE */

    .neo-switch input:checked + .neo-track{

        background:#dcfce7;

        box-shadow:
            inset 6px 6px 12px rgba(187,247,208,.9),
            inset -6px -6px 12px rgba(255,255,255,.9);

    }

    /* MOVE */

    .neo-switch input:checked + .neo-track .neo-thumb{

        transform:translateX(46px);

        background:#22c55e;

        box-shadow:
            4px 4px 12px rgba(34,197,94,.25),
            -4px -4px 12px rgba(255,255,255,.8);

    }

    /* TEXT */

    .neo-switch input:checked + .neo-track .neo-text{

        color:white;

    }

    /* OFF */

    .neo-switch input:not(:checked) + .neo-track .neo-text::before{

        content:"OFF";

    }

    /* ON */

    .neo-switch input:checked + .neo-track .neo-text::before{

        content:"ON";

    }

    /* sembunyikan text asli */

    .neo-text{

        font-size:0;

    }

    /* Hover */

    .neo-switch:hover .neo-thumb{

        transform:scale(1.05);

    }

    .neo-switch input:checked + .neo-track .neo-thumb{

        transform:translateX(46px);

    }

    .neo-switch input:checked:hover + .neo-track .neo-thumb{

        transform:translateX(46px) scale(1.05);

    }
</style>

<table id="salesTable">
    <thead>
        <tr>
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
            $statusClass='stock-empty';
            $statusIcon='fa-circle-xmark';
        }elseif($stock <= 50){
            $statusText='Menipis';
            $statusClass='stock-low';
            $statusIcon='fa-triangle-exclamation';
        }else{
            $statusText='Aman';
            $statusClass='stock-success';
            $statusIcon='fa-circle-check';
        }

        $isActive = ($d['catalog'] === 'active');
    ?>

    <tr class="sales-row <?= $isActive ? 'catalog-active' : '' ?>" id="row-<?= $d['id'] ?>">
        <td>
            <div class="product-wrap">
                <div class="product-icon">
                    <i class="fas fa-box"></i>
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

        <td class="text-center">
            <label class="neo-switch">

                <input
                    type="checkbox"
                    <?= $isActive ? 'checked' : '' ?>
                    onchange="toggleCatalog(<?= $d['id'] ?>, this)">

                <span class="neo-track">
                    <span class="neo-thumb">
                        <span class="neo-text">OFF</span>
                    </span>
                </span>

            </label>
        </td>
    </tr>

    <?php endwhile; ?>

    </tbody>
</table>