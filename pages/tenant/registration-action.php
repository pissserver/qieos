<?php
    include '../../sessions/session.php';
    header('Content-Type: application/json');

    if ($_GET['action']=='store') {
        $tenant_name = $_POST['tenant_name'];
        $tenant_owner    = $_POST['tenant_owner'];
        $dateNow = date('Y-m-d');

        mysqli_query($conn,"
            INSERT INTO tenants (tenant_name, tenant_owner, status, registration_date)
            VALUES ('$tenant_name', '$tenant_owner', 'active', '$dateNow')
        ");

        echo json_encode([
            'status' => 'success',
            'message' => 'Data berhasil ditambahkan.'
        ]);
    }

    if($_GET['action']=='update'){

        $id = $_POST['id'];

        $tenant_name = $_POST['tenant_name'];
        $tenant_owner = $_POST['tenant_owner'];

        mysqli_query($conn,"
            UPDATE tenants 
            SET tenant_name='$tenant_name',
                tenant_owner='$tenant_owner'
            WHERE id = '$id'
        ");

        echo json_encode([
            'status' => 'success',
            'message' => 'Data berhasil diupdate.'
        ]);

        exit;
    }

    if($_GET['action']=='destroy'){

        $id = $_POST['id'];
        $dateNow = date('Y-m-d');

        mysqli_query($conn,"
            UPDATE tenants 
            SET status = 'inactive',
                deleted_at = '$dateNow'
            WHERE id = '$id'
        ");

        echo json_encode([
            'status' => 'success',
            'message' => 'Data berhasil dihapus.'
        ]);

        exit;
    }