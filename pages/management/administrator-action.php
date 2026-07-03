<?php
    include '../../sessions/session.php';
    header('Content-Type: application/json');

    if ($_GET['action']=='store') {
        $fullname = $_POST['fullname'];
        $email    = $_POST['email'];
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        $dateNow = date('Y-m-d');

        // Cek email sudah digunakan
        $check_email = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
        if (mysqli_num_rows($check_email) > 0) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Email sudah digunakan.'
            ]);
            exit;
        }

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
            INSERT INTO users (fullname, email, password, role, created_at)
            VALUES ('$fullname', '$email', '$hashed_password', 'administrator', '$dateNow')
        ");

        echo json_encode([
            'status' => 'success',
            'message' => 'Data berhasil ditambahkan.'
        ]);
    }

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

    if($_GET['action']=='destroy'){

        $id = $_POST['id'];

        mysqli_query($conn,"
            DELETE FROM users 
            WHERE id = '$id'
        ");

        echo json_encode([
            'status' => 'success',
            'message' => 'Data berhasil dihapus.'
        ]);

        exit;
    }