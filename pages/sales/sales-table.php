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

    /* TOGGLE */
    .toggle-switch{
        position:relative;
        width:95px;
        height:36px;
        display:inline-block;
    }

    .toggle-switch input{
        opacity:0;
        width:0;
        height:0;
    }

    .slider{
        position:absolute;
        inset:0;
        background:#e5e7eb;
        border-radius:999px;
        cursor:pointer;
        transition:.3s;
        display:flex;
        align-items:center;
        justify-content:space-between;
        padding:0 12px;
        font-size:12px;
        font-weight:700;
        color:#64748b;
    }

    .slider:before{
        content:"";
        position:absolute;
        height:28px;
        width:28px;
        left:4px;
        bottom:4px;
        background:#fff;
        border-radius:50%;
        transition:.3s;
        box-shadow:0 3px 10px rgba(0,0,0,.15);
    }

    input:checked + .slider{
        background:linear-gradient(135deg,#22c55e,#16a34a);
        color:#fff;
    }

    input:checked + .slider:before{
        transform:translateX(58px);
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
            <label class="toggle-switch">
                <input type="checkbox"
                    <?= $isActive ? 'checked' : '' ?>
                    onchange="toggleCatalog(<?= $d['id'] ?>, this)">

                <span class="slider">
                    <span><i class="fas fa-times"></i></span>
                    <span><i class="fas fa-check"></i></span>
                </span>
            </label>
        </td>
    </tr>

    <?php endwhile; ?>

    </tbody>
</table>