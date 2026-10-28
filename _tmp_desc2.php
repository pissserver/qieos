<?php
require_once __DIR__ . '/script/connection.php';
$q = mysqli_query($conn, "DESCRIBE purchase_items");
while ($r = mysqli_fetch_assoc($q)) {
    echo $r['Field'] . ' | ' . $r['Type'] . "\n";
}
mysqli_close($conn);
