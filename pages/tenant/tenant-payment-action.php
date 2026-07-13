<?php
    include '../../sessions/session.php';
    header('Content-Type: application/json');

    if($_GET['action']=='store') {
        
        $type = $_POST['type'];
        $tenant_id = $_POST['tenant_id'];
        $cost_payment = $_POST['cost_payment'];
        $payment_date = $_POST['payment_date'];
        $staff_id = $_SESSION['user_id'];

        if($type === 'utility'){
            $table = 'utility_payments';
        }else{
            $table = 'tenant_payments';
        }

        mysqli_query($conn,"
            INSERT INTO $table (staff_id, tenant_id, cost_payment, payment_date, status)
            VALUES ('$staff_id', '$tenant_id', '$cost_payment', '$payment_date', 'paid')
        ");

        $payment_id = mysqli_insert_id($conn);

        echo json_encode([
            'status' => 'success',
            'message' => 'Data berhasil ditambahkan.',
            'payment_id' => $payment_id,
            'type' => $type
        ]);

        exit;
    }

    if($_GET['action']=='update'){

        $id = $_POST['id'];
        $type = $_POST['type'];
        $tenant_id = $_POST['tenant_id'];
        $payment_date = $_POST['payment_date'];

        if($type === 'utility'){
            $table = 'utility_payments';   
        }else{
            $table = 'tenant_payments';
        }

        mysqli_query($conn,"
            UPDATE $table 
            SET tenant_id='$tenant_id',
                payment_date='$payment_date'
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
        $type = $_POST['type'];

        if($type === 'utility'){
            $table = 'utility_payments';
        }else{
            $table = 'tenant_payments';
        }

        mysqli_query($conn,"
            DELETE FROM $table
            WHERE id = '$id' 
        ");

        echo json_encode([
            'status' => 'success',
            'message' => 'Data berhasil dihapus.'
        ]);

        exit;
    }