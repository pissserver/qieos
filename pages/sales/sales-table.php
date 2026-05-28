<?php
    include '../../sessions/session.php';

    $q = mysqli_query($conn,"
    SELECT s.qty, p.name, p.code
    FROM sales_stock s
    JOIN products p ON p.id=s.product_id
    ORDER BY p.name ASC
    ");
?>

<table id="salesTable" class="table table-hover">
    <thead>
        <tr>
            <th>Produk</th>
            <th class="text-center">Stok</th>
        </tr>
    </thead>

    <tbody>
        <?php while($d=mysqli_fetch_assoc($q)): ?>
        <tr>
            <td>
                <b><?= $d['name'] ?></b><br />
                <small><?= $d['code'] ?></small>
            </td>

            <td style="vertical-align: middle;" class="text-center">
                <span class="badge bg-primary"><?= $d['qty'] ?></span>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
