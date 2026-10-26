<?php

include '../../sessions/session.php';

$id = (int)$_POST['id'];

$q = mysqli_query($conn,"
    SELECT starred
    FROM products
    WHERE id = $id
");

$row = mysqli_fetch_assoc($q);

$newValue = $row['starred'] ? 0 : 1;

mysqli_query($conn,"
    UPDATE products
    SET starred = $newValue
    WHERE id = $id
");

echo json_encode([
    'starred'=>$newValue
]);