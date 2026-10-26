<?php
include '../../sessions/session.php';
header('Content-Type: application/json');

$action = isset($_GET['action']) ? $_GET['action'] : '';

// STORE
if($action === 'store'){

    $name  = mysqli_real_escape_string($conn, trim(isset($_POST['name']) ? $_POST['name'] : ''));
    $phone = mysqli_real_escape_string($conn, trim(isset($_POST['phone']) ? $_POST['phone'] : ''));

    if($name === ''){
        echo json_encode(['status'=>'error', 'message'=>'Nama customer wajib diisi']);
        exit;
    }

    $q = mysqli_query($conn,"INSERT INTO customers (name,phone,created_at,updated_at) VALUES ('$name','$phone',NOW(),NOW())");
    if($q){ echo json_encode(['status'=>'success']); }
    else{ echo json_encode(['status'=>'error', 'message'=>'Gagal menyimpan data']); }
    exit;
}

// UPDATE
if($action === 'update'){

    $id    = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $name  = mysqli_real_escape_string($conn, trim(isset($_POST['name']) ? $_POST['name'] : ''));
    $phone = mysqli_real_escape_string($conn, trim(isset($_POST['phone']) ? $_POST['phone'] : ''));

    if($id <= 0 || $name === ''){
        echo json_encode(['status'=>'error', 'message'=>'Semua field wajib diisi']);
        exit;
    }

    $q = mysqli_query($conn,"UPDATE customers SET name='$name',phone='$phone',updated_at=NOW() WHERE id=$id");
    if($q){ echo json_encode(['status'=>'success']); }
    else{ echo json_encode(['status'=>'error', 'message'=>'Gagal memperbarui data']); }
    exit;
}

// DESTROY
if($action === 'destroy'){
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    if($id <= 0){ echo json_encode(['status'=>'error', 'message'=>'ID tidak valid']); exit; }
    $q = mysqli_query($conn,"UPDATE customers SET deleted_at = NOW() WHERE id = $id");
    if($q){ echo json_encode(['status'=>'success']); }
    else{ echo json_encode(['status'=>'error', 'message'=>'Gagal menghapus data']); }
    exit;
}

echo json_encode(['status'=>'error', 'message'=>'Aksi tidak dikenali']);