<?php
    session_start();
    include '../../script/connection.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['photo'])) {
        $file = $_FILES['photo'];
        $targetDir = __DIR__ . "/../../assets/img/uploads/"; // path absolut benar
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $fileName = basename($file["name"]);
        $targetFile = $targetDir . $fileName;

        // Validasi type dan ekstensi
        $allowedTypes = ['image/jpeg','image/png','image/gif'];
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExt = ['jpg','jpeg','png','gif'];

        if (in_array($file['type'], $allowedTypes) && in_array($ext, $allowedExt) && $file['size'] <= 2000000) {
            
            // --- Cek foto lama ---
            $sqlOld = "SELECT photo FROM users WHERE username=?";
            $stmtOld = mysqli_prepare($conn, $sqlOld);
            mysqli_stmt_bind_param($stmtOld, "s", $_SESSION['username']);
            mysqli_stmt_execute($stmtOld);
            mysqli_stmt_bind_result($stmtOld, $oldPhoto);
            mysqli_stmt_fetch($stmtOld);
            mysqli_stmt_close($stmtOld);

            // Hapus file lama jika ada dan bukan default
            if ($oldPhoto && file_exists(__DIR__ . "../../" . $oldPhoto) && strpos($oldPhoto, "default-avatar.png") === false) {
                unlink(__DIR__ . "../../" . $oldPhoto);
            }

            // --- Simpan file baru ---
            if (move_uploaded_file($file["tmp_name"], $targetFile)) {
                $dbPath = $fileName;

                $sql = "UPDATE users SET photo=? WHERE username=?";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "ss", $dbPath, $_SESSION['username']);
                mysqli_stmt_execute($stmt);

                $_SESSION['flash'] = "Foto profil berhasil diperbarui.";
            } else {
                $_SESSION['flash'] = "Gagal upload file.";
            }
        } else {
            $_SESSION['flash'] = "Format/ukuran file tidak valid.";
        }

        header("Location: profile.php");
        exit();
    }
?>