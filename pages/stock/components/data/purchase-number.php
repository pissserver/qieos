<?php
function next_purchase_form_number($conn){
    $yearPrefix = date('y');
    $query = mysqli_query($conn, "
        SELECT MAX(CAST(SUBSTRING(form, 8, 7) AS UNSIGNED)) AS last_num
        FROM purchases
        WHERE LEFT(form, 7) = 'FORM-$yearPrefix'
          AND CHAR_LENGTH(form) = 14
          AND SUBSTRING(form, 8, 7) REGEXP '^[0-9]{7}$'
    ");
    if(!$query) return false;

    $row = mysqli_fetch_assoc($query);
    $nextNumber = ($row && $row['last_num'] !== null ? (int)$row['last_num'] : 0) + 1;
    return 'FORM-' . $yearPrefix . str_pad($nextNumber, 7, '0', STR_PAD_LEFT);
}
