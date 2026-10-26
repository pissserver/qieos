<?php
    include '../../sessions/session.php';

    header('Content-Type: application/json');

    $data = json_decode(file_get_contents("php://input"), true);

    $id = (int)$data['id'];
    $status = ($data['status'] === 'active') ? 'active' : 'nonactive';

    $q = mysqli_query($conn,"
    UPDATE products
    SET catalog = '$status'
    WHERE id = $id
    ");

    echo json_encode([
        "success" => $q ? true : false
    ]);
exit;