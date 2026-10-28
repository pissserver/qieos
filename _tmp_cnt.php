<?php
require_once __DIR__ . '/script/connection.php';
$q = mysqli_query($conn, "SELECT COUNT(*) c FROM orders WHERE deleted_at IS NULL AND status_payment='paid'");
if ($q) {
    $r = mysqli_fetch_assoc($q);
    echo $r['c'];
} else {
    echo mysqli_error($conn);
}
mysqli_close($conn);
