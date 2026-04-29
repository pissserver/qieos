<?php
    include '../script/connection.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = isset($_POST['name']) ? $_POST['name'] : '';
        $email = isset($_POST['email']) ? $_POST['email'] : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
        $created_at = date("Y-m-d");

        // Validasi input
        if($email==''||$password==''||$confirm_password==''){
            header("Location:sign-up.php?error=empty");
            exit;
        }

        $check=mysqli_query($conn,"SELECT id FROM users WHERE email='$email'");
        if(mysqli_num_rows($check)>0){
            header("Location:sign-up.php?error=email");
            exit;
        }
        
        if($password!=$confirm_password){
            header("Location:sign-up.php?error=password");
            exit;
        }


        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Simpan ke database
        $sql = "INSERT INTO users (email, password, fullname, created_at) VALUES ('$email', '$hashed_password', '$name', '$created_at')";
        
        if ($conn->query($sql) === TRUE) {
            header("Location:sign-in.php?success=register");
            exit;
        } else {
            echo "<script>alert('Terjadi error: ".$conn->error."');history.back();</script>";
        }

        $conn->close();
    }
?>