<?php

include '../../../sessions/session.php';

$type = $_GET['type'];

$first_date = $_GET['first_date'];
$last_date  = $_GET['last_date'];

$table = ($type == 'tenant')
    ? 'tenant_payments'
    : 'utility_payments';

$query = mysqli_query($conn,"
SELECT
    p.*,
    t.tenant_name
FROM $table p
JOIN tenants t
ON p.tenant_id = t.id
WHERE DATE(payment_date)
BETWEEN '$first_date'
AND '$last_date'
ORDER BY payment_date DESC
");

$no = 1;

while($row=mysqli_fetch_assoc($query)){
?>

<tr>

    <td class="text-center"><?= $no++ ?></td>

    <td class="text-center"><?= $row['tenant_name'] ?></td>

    <td class="text-center"><?= date('d M Y',strtotime($row['payment_date'])) ?></td>

    <td class="text-center">
        Rp <?= number_format($row['cost_payment'],0,',','.') ?>
    </td>

    <td class="text-center">
        <span class="status-paid">
            Lunas
        </span>
    </td>

</tr>

<?php } ?>