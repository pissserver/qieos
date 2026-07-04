<?php
include '../../sessions/session.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = $_POST['fullname'];

    $password = $_POST['password'] ? $_POST['password'] : '';
    $password_confirm = $_POST['password_confirm'] ? $_POST['password_confirm'] : '';

    $email = $_SESSION['email'];

    // update password hanya jika diisi
    if (!empty($password)) {
        if ($password !== $password_confirm) {
            $_SESSION['flash'] = "Password tidak sama.";
            header("Location: profile.php?status=error&msg=Password tidak sama");
            exit();
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql = "UPDATE users 
                SET fullname='$fullname', 
                    email='$email', 
                    password='$hashedPassword'
                WHERE email='$email'";

        $resultPassword = mysqli_query($conn, $sql);
    } else {
        $sql = "UPDATE users 
                SET fullname='$fullname'
                WHERE email='$email'";

        $result = mysqli_query($conn, $sql);
    }


    if ($resultPassword) {
        session_destroy();
        header("Location: ../sessions/sign-in.php?success=reset");
        exit();
    } elseif ($result) {
        $_SESSION['flash'] = "Profil berhasil diperbarui.";
        header("Location: profile.php?status=success&msg=Profil berhasil diperbarui");
        exit();
    } else {
        $_SESSION['flash'] = "Profile gagal diperbarui.";
        header("Location: profile.php?status=error&msg=Profil gagal diperbarui");
        exit();
    }
}
