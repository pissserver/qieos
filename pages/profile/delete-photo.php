<?php
include '../../sessions/session.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_SESSION['username'];

    // Ambil nama file foto lama
    $sqlOld = "SELECT photo FROM users WHERE username=?";
    $stmtOld = mysqli_prepare($conn, $sqlOld);
    mysqli_stmt_bind_param($stmtOld, "s", $username);
    mysqli_stmt_execute($stmtOld);
    mysqli_stmt_bind_result($stmtOld, $oldPhoto);
    mysqli_stmt_fetch($stmtOld);
    mysqli_stmt_close($stmtOld);

    // Hapus file fisik jika ada dan bukan default
    if ($oldPhoto && strpos($oldPhoto, "default-avatar") === false && strpos($oldPhoto, "profile-default") === false) {
        $filePath = __DIR__ . "/../../assets/img/uploads/" . $oldPhoto;
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    // Set photo NULL di database
    $sql = "UPDATE users SET photo = NULL WHERE username = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $username);
    
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['flash'] = "Foto profil berhasil dihapus.";
    } else {
        $_SESSION['flash'] = "Gagal menghapus foto profil.";
    }

    header("Location: profile.php");
    exit();
}
?>