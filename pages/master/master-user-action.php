<?php
    include '../../sessions/session.php';
    header('Content-Type: application/json');

    $allowed_roles = ['administrator', 'staff kasir'];
    $action = isset($_GET['action']) ? $_GET['action'] : '';

    function respond($status, $message, $role = null) {
        $data = ['status' => $status, 'message' => $message];
        if ($role !== null) {
            $data['role'] = $role;
        }
        echo json_encode($data);
        exit;
    }

    if ($action == 'store') {
        $fullname = isset($_POST['fullname']) ? trim($_POST['fullname']) : '';
        $username = isset($_POST['username']) ? trim($_POST['username']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
        $role = isset($_POST['role']) ? $_POST['role'] : '';
        $dateNow = date('Y-m-d');

        // Cek role valid
        if (!in_array($role, $allowed_roles, true)) {
            respond('error', 'Role tidak valid.');
        }

        // Cek username sudah digunakan
        $check_username = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
        if (mysqli_num_rows($check_username) > 0) {
            respond('error', 'Username sudah digunakan.');
        }

        // Cek confirm password
        if ($password != $confirm_password) {
            respond('error', 'Konfirmasi password tidak sesuai.');
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        mysqli_query($conn,"
            INSERT INTO users (fullname, username, password, role, created_at)
            VALUES ('$fullname', '$username', '$hashed_password', '$role', '$dateNow')
        ");

        respond('success', 'Data berhasil ditambahkan.', $role);
    }

    if ($action == 'update') {
        $id = (int)$_POST['id'];

        $fullname = isset($_POST['fullname']) ? trim($_POST['fullname']) : '';
        $username = isset($_POST['username']) ? trim($_POST['username']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
        $role = isset($_POST['role']) ? $_POST['role'] : '';

        // Ambil data lama
        $check = mysqli_query($conn, "SELECT * FROM users WHERE id = '$id'");
        if (mysqli_num_rows($check) < 1) {
            respond('error', 'User tidak ditemukan.');
        }
        $old = mysqli_fetch_assoc($check);

        // Developer tidak boleh diubah
        if ($old['role'] === 'developer') {
            respond('error', 'Akun developer tidak dapat diubah.');
        }

        // Cek role valid
        if (!in_array($role, $allowed_roles, true)) {
            respond('error', 'Role tidak valid.');
        }

        // Akun sendiri: tidak boleh mengubah role / menonaktifkan diri sendiri
        if ($old['username'] === $_SESSION['username']) {
            if ($role !== 'administrator') {
                respond('error', 'Anda tidak dapat mengubah role akun Anda sendiri.');
            }
        }

        // Cek username belum dipakai user lain
        $check_username = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username' AND id != '$id'");
        if (mysqli_num_rows($check_username) > 0) {
            respond('error', 'Username sudah digunakan.');
        }

        // Update password hanya jika diisi
        if (!empty($password)) {
            if ($password != $confirm_password) {
                respond('error', 'Konfirmasi password tidak sesuai.');
            }

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            mysqli_query($conn,"
                UPDATE users
                SET fullname='$fullname',
                    username='$username',
                    password='$hashed_password',
                    role='$role'
                WHERE id = '$id'
            ");
        } else {
            mysqli_query($conn,"
                UPDATE users
                SET fullname='$fullname',
                    username='$username',
                    role='$role'
                WHERE id = '$id'
            ");
        }

        respond('success', 'Data berhasil diupdate.', $role);
    }

    if ($action == 'destroy') {
        $id = (int)$_POST['id'];

        // Ambil data user
        $check = mysqli_query($conn, "SELECT * FROM users WHERE id = '$id'");
        if (mysqli_num_rows($check) < 1) {
            respond('error', 'User tidak ditemukan.');
        }
        $old = mysqli_fetch_assoc($check);

        // Developer tidak boleh dihapus
        if ($old['role'] === 'developer') {
            respond('error', 'Akun developer tidak dapat dihapus.');
        }

        // Akun sendiri tidak boleh dihapus
        if ($old['username'] === $_SESSION['username']) {
            respond('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        mysqli_query($conn,"
            DELETE FROM users
            WHERE id = '$id'
        ");

        respond('success', 'Data berhasil dihapus.', $old['role']);
    }

    respond('error', 'Aksi tidak dikenal.');