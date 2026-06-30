<?php
    include '../../sessions/session.php';
    header('Content-Type: application/json');

    if($_GET['action']=='update'){

        $id = $_POST['id'];

        $fullname = $_POST['fullname'];
        $email    = $_POST['email'];
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        // Cek confirm password
        if ($password != $confirm_password) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Konfirmasi password tidak sesuai.'
            ]);
            exit;
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        mysqli_query($conn,"
            UPDATE users 
            SET fullname='$fullname',
                email='$email',
                password='$hashed_password'
            WHERE id = '$id'
        ");

        echo json_encode([
            'status' => 'success',
            'message' => 'Data berhasil diupdate.'
        ]);

        exit;
    }