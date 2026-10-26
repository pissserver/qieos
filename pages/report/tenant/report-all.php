<?php

include '../../../sessions/session.php';

$type       = isset($_GET['type']) ? $_GET['type'] : 'tenant';
$first_date = isset($_GET['first_date']) ? $_GET['first_date'] : '';
$last_date  = isset($_GET['last_date']) ? $_GET['last_date'] : '';

$table = ($type == 'tenant')
    ? 'tenant_payments'
    : 'utility_payments';

$escFirst = mysqli_real_escape_string($conn, $first_date);
$escLast  = mysqli_real_escape_string($conn, $last_date);

$query = mysqli_query($conn, "
SELECT
    p.*,
    t.tenant_name
FROM $table p
JOIN tenants t
ON p.tenant_id = t.id
WHERE DATE(payment_date)
BETWEEN '$escFirst'
AND '$escLast'
ORDER BY payment_date DESC
");

$no = 1;
$totalPayment = 0;
$hasData = false;

if ($query && mysqli_num_rows($query) > 0) {
    $hasData = true;
    while ($row = mysqli_fetch_assoc($query)) {
        $cost = (float) $row['cost_payment'];
        $totalPayment += $cost;
        ?>

<tr>

    <td class="text-center"><?= $no++ ?></td>

    <td class="text-center fw-bold"><?= htmlspecialchars($row['tenant_name']) ?></td>

    <td class="text-center"><?= date('d M Y', strtotime($row['payment_date'])) ?></td>

    <td class="text-center fw-semibold">
        Rp <?= number_format($cost, 0, ',', '.') ?>
    </td>

    <td class="text-center">
        <span class="status-paid">
            Lunas
        </span>
    </td>

</tr>

<?php
    }
} else {
    ?>
<tr class="report-empty-row">
    <td colspan="5" class="text-center py-4 text-muted">
        <i class="fas fa-file-invoice-dollar mb-2 style-2x" style="font-size:24px;"></i>
        <div>Tidak ada data pembayaran pada periode ini.</div>
    </td>
</tr>
<?php
}

echo "<!--SPLIT_FOOT-->";
echo "Rp " . number_format($totalPayment, 0, ',', '.');
