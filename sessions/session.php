<?php
    session_start();
    require_once __DIR__ . '/../script/connection.php';
    date_default_timezone_set('Asia/Jakarta');
    
    if (!isset($_SESSION['username'])) {
        header("Location: ../sessions/sign-in.php");
        exit();
    }

    $sql = "SELECT * FROM users WHERE username='".$_SESSION['username']."'";
    $result = mysqli_query($conn, $sql);
    $user = mysqli_fetch_assoc($result);
?>