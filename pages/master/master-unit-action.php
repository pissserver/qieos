<?php
include '../../sessions/session.php';
header('Content-Type: application/json');

$action = isset($_GET['action']) ? $_GET['action'] : '';

// STORE
if($action === 'store'){

    $name = mysqli_real_escape_string($conn, trim(isset($_POST['name']) ? $_POST['name'] : ''));
    $note = mysqli_real_escape_string($conn, trim(isset($_POST['note']) ? $_POST['note'] : ''));

    if($name === ''){
        echo json_encode(['status'=>'error', 'message'=>'Nama unit wajib diisi']);
        exit;
    }

    $dup = mysqli_query($conn, "SELECT id FROM units WHERE LOWER(name)=LOWER('$name') AND deleted_at IS NULL LIMIT 1");
    if($dup && mysqli_num_rows($dup) > 0){
        echo json_encode(['status'=>'error', 'message'=>'Nama unit sudah ada']);
        exit;
    }

    $q = mysqli_query($conn,"INSERT INTO units (name,note,created_at,updated_at) VALUES ('$name','$note',NOW(),NOW())");
    if($q){ echo json_encode(['status'=>'success']); }
    else{ echo json_encode(['status'=>'error', 'message'=>'Gagal menyimpan data']); }
    exit;
}

// UPDATE
if($action === 'update'){

    $id   = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $name = mysqli_real_escape_string($conn, trim(isset($_POST['name']) ? $_POST['name'] : ''));
    $note = mysqli_real_escape_string($conn, trim(isset($_POST['note']) ? $_POST['note'] : ''));

    if($id <= 0 || $name === ''){
        echo json_encode(['status'=>'error', 'message'=>'Semua field wajib diisi']);
        exit;
    }

    $dup = mysqli_query($conn, "SELECT id FROM units WHERE LOWER(name)=LOWER('$name') AND deleted_at IS NULL AND id <> $id LIMIT 1");
    if($dup && mysqli_num_rows($dup) > 0){
        echo json_encode(['status'=>'error', 'message'=>'Nama unit sudah dipakai unit lain']);
        exit;
    }

    $q = mysqli_query($conn,"UPDATE units SET name='$name',note='$note',updated_at=NOW() WHERE id=$id");
    if($q){ echo json_encode(['status'=>'success']); }
    else{ echo json_encode(['status'=>'error', 'message'=>'Gagal memperbarui data']); }
    exit;
}

// DESTROY
if($action === 'destroy'){
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    if($id <= 0){ echo json_encode(['status'=>'error', 'message'=>'ID tidak valid']); exit; }
    $q = mysqli_query($conn,"UPDATE units SET deleted_at = NOW() WHERE id = $id");
    if($q){ echo json_encode(['status'=>'success']); }
    else{ echo json_encode(['status'=>'error', 'message'=>'Gagal menghapus data']); }
    exit;
}

echo json_encode(['status'=>'error', 'message'=>'Aksi tidak dikenali']);
