<?php

    include '../script/connection.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = isset($_POST['username']) ? $_POST['username'] : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';

        // Validasi input
        if($username==''||$password==''){
            header("Location:sign-in.php?error=empty");
            exit;
        }

        // Cek username di database
        $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                // Login berhasil
                session_start();
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];

                // Jika user centang "ingat saya"
                if (isset($_POST['remember'])) {
                    setcookie("username", $row['username'], time() + (86400 * 30), "/"); // 30 hari
                } else {
                    setcookie("username", "", time() - 3600, "/"); // Hapus cookie
                }

                header("Location:../index.php");
                exit;
            } else {
                // Password salah
                header("Location:sign-in.php?error=password");
                exit;
            }
        } else {
            // Username tidak ditemukan
            header("Location:sign-in.php?error=username");
            exit;
        }

        $conn->close();
    }