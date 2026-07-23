<?php
    include '../../sessions/session.php';
    header('Content-Type: application/json');

    if ($_GET['action'] == 'store') {

        $update_name    = mysqli_real_escape_string($conn, $_POST['update_name']);
        $update_version = mysqli_real_escape_string($conn, $_POST['update_version']);
        $update_type    = mysqli_real_escape_string($conn, $_POST['update_type']);
        $update_date    = $_POST['update_date'];

        $descriptions = $_POST['description'];

        // Validasi minimal ada 1 deskripsi
        if (empty($descriptions) || count($descriptions) == 0) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Minimal harus ada satu deskripsi update.'
            ]);
            exit;
        }

        // Simpan ke tabel updates
        mysqli_query($conn, "
            INSERT INTO updates
            (
                update_name,
                update_version,
                update_type,
                update_date
            )
            VALUES
            (
                '$update_name',
                '$update_version',
                '$update_type',
                '$update_date'
            )
        ");

        // Ambil ID update yang baru
        $update_id = mysqli_insert_id($conn);

        // Simpan semua detail
        foreach ($descriptions as $description) {

            $description = trim($description);

            if ($description == '') {
                continue;
            }

            $description = mysqli_real_escape_string($conn, $description);

            mysqli_query($conn, "
                INSERT INTO update_details
                (
                    update_id,
                    description
                )
                VALUES
                (
                    '$update_id',
                    '$description'
                )
            ");

        }

        echo json_encode([
            'status' => 'success',
            'message' => 'Log update berhasil ditambahkan.'
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