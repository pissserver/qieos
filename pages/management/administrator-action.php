<?php
    include '../../sessions/session.php';
    header('Content-Type: application/json');

    if ($_GET['action']=='store') {
        $fullname = $_POST['fullname'];
        $username    = $_POST['username'];
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        $dateNow = date('Y-m-d');

        // Cek username sudah digunakan
        $check_username = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
        if (mysqli_num_rows($check_username) > 0) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Username sudah digunakan.'
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
            INSERT INTO users (fullname, username, password, role, created_at)
            VALUES ('$fullname', '$username', '$hashed_password', 'administrator', '$dateNow')
        ");

        echo json_encode([
            'status' => 'success',
            'message' => 'Data berhasil ditambahkan.'
        ]);
    }

    if($_GET['action']=='update'){

        $id = $_POST['id'];

        $fullname = $_POST['fullname'];
        $username    = $_POST['username'];
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
                username='$username',
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