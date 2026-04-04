<?php
    include '../sessions/session.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $fullname = isset($_POST['fullname']) ? $_POST['fullname'] : '';
        $email = isset($_POST['email']) ? $_POST['email'] : '';
        $job = isset($_POST['job']) ? $_POST['job'] : '';
        $city = isset($_POST['city']) ? $_POST['city'] : '';
        $state = isset($_POST['state']) ? $_POST['state'] : '';

        $sql = "UPDATE users SET fullname='$fullname', email='$email', job='$job', city='$city', state='$state' WHERE email='$email'";
        $result = mysqli_query($conn, $sql);

        if ($result) {
            $_SESSION['flash'] = "Profil berhasil diperbarui.";
            header("Location: profile.php?status=success&msg=Profil berhasil diperbarui");
            exit();
        } else {
            $_SESSION['flash'] = "Profile gagal diperbarui.";
            header("Location: profile.php?status=error&msg=Profil gagal diperbarui");
            exit();
        }
    }