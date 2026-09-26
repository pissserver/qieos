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

        if ($file['size'] > 2000000) {
            $sizeMB = number_format($file['size'] / 1048576, 2);
            $_SESSION['flash'] = "Gagal: ukuran file {$sizeMB} MB melebihi batas maksimal 2 MB. Silakan pilih gambar yang lebih kecil.";
            error_log("[UPLOAD] Rejected: size {$sizeMB}MB > 2MB");
        } elseif (!in_array($file['type'], $allowedTypes) || !in_array($ext, $allowedExt)) {
            $_SESSION['flash'] = "Gagal: format file tidak didukung. Gunakan JPG, PNG, atau GIF.";
            error_log("[UPLOAD] Rejected: type={$file['type']} ext={$ext}");
        } else {
            
            // --- Cek foto lama ---
            $sqlOld = "SELECT photo FROM users WHERE username=?";
            $stmtOld = mysqli_prepare($conn, $sqlOld);
            mysqli_stmt_bind_param($stmtOld, "s", $_SESSION['username']);
            mysqli_stmt_execute($stmtOld);
            mysqli_stmt_bind_result($stmtOld, $oldPhoto);
            mysqli_stmt_fetch($stmtOld);
            mysqli_stmt_close($stmtOld);

            // Hapus file lama jika ada dan bukan default
            $oldFilePath = __DIR__ . "/../../assets/img/uploads/" . $oldPhoto;
            if ($oldPhoto
                && strpos($oldPhoto, "default-avatar") === false
                && strpos($oldPhoto, "profile-default") === false
                && file_exists($oldFilePath)) {
                unlink($oldFilePath);
            }

            // --- Simpan file baru ---
            if (move_uploaded_file($file["tmp_name"], $targetFile)) {
                $dbPath = $fileName;

                $sql = "UPDATE users SET photo=? WHERE username=?";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "ss", $dbPath, $_SESSION['username']);
                mysqli_stmt_execute($stmt);

                $_SESSION['flash'] = "Foto profil berhasil diperbarui.";
                error_log("[UPLOAD] OK: {$fileName} size=" . number_format($file['size']/1048576, 2) . "MB user={$_SESSION['username']}");
            } else {
                $_SESSION['flash'] = "Gagal upload file.";
                error_log("[UPLOAD] move_uploaded_file FAILED: tmp={$file['tmp_name']} err=" . json_encode(error_get_last()));
            }
        }

        header("Location: profile.php");
        exit();
    }
?>