<?php
    include '../../sessions/session.php';

    $q = mysqli_query($conn,"
    SELECT
        s.qty,
        p.id,
        p.name,
        p.code,
        p.photo,
        p.catalog
    FROM sales_stock s
    JOIN products p
        ON p.id = s.product_id
    ORDER BY p.name ASC
    ");
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/sales-table.css">

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
            $statusText='Ready';
            $statusClass='stock-success';
            $statusIcon='fa-circle-check';
        }

        $isActive = ($d['catalog'] === 'active');
    ?>

    <tr class="sales-row <?= $isActive ? 'catalog-active' : '' ?>" id="row-<?= $d['id'] ?>">
        <td>
            <div class="product-wrap">
                <?php if(!empty($d['photo'])): ?>
                <img class="product-img"
                    src="<?= BASE_URL ?>/assets/img/products/<?= htmlspecialchars($d['photo']) ?>"
                    alt="<?= htmlspecialchars($d['name']) ?>">
                <?php else: ?>
                <div class="product-icon">
                    <i class="fas fa-box"></i>
                </div>
                <?php endif; ?>
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