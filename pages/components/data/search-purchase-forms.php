<?php
include '../../../sessions/session.php';

header('Content-Type: application/json');

$search = isset($_GET['q']) ? mysqli_real_escape_string($conn, trim($_GET['q'])) : '';

$where = "p.deleted_at IS NULL";
if ($search !== '') {
    $where .= " AND (p.form LIKE '%$search%' OR DATE_FORMAT(p.date, '%d/%m/%Y') LIKE '%$search%')";
}

$query = mysqli_query($conn, "
    SELECT p.id, p.form, p.date
    FROM purchases p
    WHERE $where
    ORDER BY p.date DESC, p.id DESC
    LIMIT 50
");

$results = array();

if ($query) {
    while ($row = mysqli_fetch_assoc($query)) {
        $results[] = array(
            'id' => $row['id'],
            'text' => $row['form'] . ' - ' . date('d/m/Y', strtotime($row['date']))
        );
    }
}

echo json_encode($results);
