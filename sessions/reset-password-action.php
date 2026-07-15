<?php
    session_start();
    include '../script/connection.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
        
        if($password!=$confirm_password){
            header("Location:reset-password.php?error=password");
            exit;
        }

        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Update database
        $sql = "UPDATE users SET password='$hashed_password' WHERE username='".$_SESSION['reset_username']."'";
        
        if ($conn->query($sql) === TRUE) {
            header("Location:sign-in.php?success=reset");
            exit;
        } else {
            echo "<script>alert('Terjadi error: ".$conn->error."');history.back();</script>";
        }

        $conn->close();
    }
?>