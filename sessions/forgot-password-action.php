<?php
    session_start();
    include '../script/connection.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = isset($_POST['username']) ? $_POST['username'] : '';

        // Cek username di database
        $sql = "SELECT username FROM users WHERE username='$username'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            $_SESSION['reset_username'] = $row['username'];
            header("Location:reset-password.php");
            exit;
        } else {
            // username tidak ditemukan
            header("Location:forgot-password.php?error=invalid");
            exit;
        }

        $conn->close();
    }