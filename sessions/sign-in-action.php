<?php

    include '../script/connection.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = isset($_POST['username']) ? $_POST['username'] : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';

        // Validasi input
        if($username==''||$password==''){
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'error' => 'empty', 'message' => 'Username dan password harus diisi']);
                exit;
            }
            header("Location:sign-in.php?error=empty");
            exit;
        }

        // Cek username di database
        $stmt = $conn->prepare("SELECT id, username, fullname, password FROM users WHERE username = ?");
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

                // AJAX check
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                    header('Content-Type: application/json');
                    $fullname = !empty($row['fullname']) ? $row['fullname'] : $row['username'];
                    echo json_encode(['status' => 'success', 'fullname' => $fullname, 'redirect' => '../pages/dashboard.php']);
                    exit;
                }

                header("Location:../pages/dashboard.php");
                exit;
            } else {
                // Password salah
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                    header('Content-Type: application/json');
                    echo json_encode(['status' => 'error', 'error' => 'password', 'message' => 'Password yang dimasukkan tidak sesuai']);
                    exit;
                }
                header("Location:sign-in.php?error=password");
                exit;
            }
        } else {
            // Username tidak ditemukan
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'error' => 'username', 'message' => 'Akun dengan username tersebut tidak terdaftar']);
                exit;
            }
            header("Location:sign-in.php?error=username");
            exit;
        }

        $conn->close();
    }
