<?php
error_reporting(0);
ini_set('display_errors', '0');
include __DIR__ . '/../../sessions/session.php';

header('Content-Type: application/json');
header('Cache-Control: no-store, no-cache, must-revalidate');
mysqli_report(MYSQLI_REPORT_OFF);

$action = isset($_GET['action']) ? $_GET['action'] : '';

// Check kode produk exist
if($action === 'check_code'){
    $code = isset($_POST['code']) ? trim($_POST['code']) : '';
    if($code === ''){
        echo json_encode(['exists' => false]);
        exit;
    }
    
    $stmt = $conn->prepare("SELECT id FROM products WHERE code = ? AND deleted_at IS NULL");
    $stmt->bind_param('s', $code);
    $stmt->execute();
    $stmt->store_result();
    $exists = $stmt->num_rows > 0;
    $stmt->close();
    
    echo json_encode(['exists' => $exists]);
    exit;
}

// Upload helper
function uploadPhoto($file){
    $allowed = ['image/jpeg','image/png','image/webp'];
    if(!in_array($file['type'], $allowed)){
        return ['ok'=>false, 'msg'=>'Format file tidak valid (JPG/PNG/WEBP)'];
    }
    if($file['size'] > 2 * 1024 * 1024){
        return ['ok'=>false, 'msg'=>'Ukuran file maksimal 2MB'];
    }

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $name = 'product_' . time() . '_' . rand(1000,9999) . '.' . $ext;
    $dest = '../../assets/img/products/' . $name;

    if(move_uploaded_file($file['tmp_name'], $dest)){
        return ['ok'=>true, 'name'=>$name];
    }
    return ['ok'=>false, 'msg'=>'Gagal upload file'];
}

// Normalisasi daftar supplier_id dari POST (mendukung scalara tau array)
function normalizeSupplierIds($raw){
    $ids = [];
    $arr = is_array($raw) ? $raw : [$raw];
    foreach($arr as $v){
        $v = trim($v);
        if($v !== '' && $v !== '0'){
            $id = (int)$v;
            if($id > 0 && !in_array($id, $ids)){
                $ids[] = $id;
            }
        }
    }
    return $ids;
}

// Simpan ulang relasi produk-supplier (hapus lama, insert baru)
function saveProductSuppliers($conn, $productId, array $supplierIds){
    mysqli_query($conn, "DELETE FROM product_supplier WHERE product_id = $productId");
    foreach($supplierIds as $sid){
        mysqli_query($conn, "INSERT INTO product_supplier (product_id, supplier_id) VALUES ($productId, $sid)");
    }
}

// Ambil daftar supplier_id lama sebuah produk
function currentSupplierIds($conn, $productId){
    $ids = [];
    $r = mysqli_query($conn, "SELECT supplier_id FROM product_supplier WHERE product_id = $productId");
    if($r){
        while($row = mysqli_fetch_assoc($r)){
            $ids[] = (int)$row['supplier_id'];
        }
    }
    return $ids;
}

// Bandingkan dua set supplier (abaikan urutan) — jika sama, jangan rewrite agar id relasi tidak terbuang
function supplierIdsEqual(array $a, array $b){
    sort($a);
    sort($b);
    return $a === $b;
}

// STORE
if($action === 'store'){

    $code         = mysqli_real_escape_string($conn, trim(isset($_POST['code']) ? $_POST['code'] : ''));
    $name         = mysqli_real_escape_string($conn, trim(isset($_POST['name']) ? $_POST['name'] : ''));
    $category     = mysqli_real_escape_string($conn, trim(isset($_POST['category']) ? $_POST['category'] : ''));
    $priceRaw     = isset($_POST['price']) ? $_POST['price'] : '';
    $price        = $priceRaw === '' ? -1 : (int)$priceRaw;
    $unit         = mysqli_real_escape_string($conn, trim(isset($_POST['unit']) ? $_POST['unit'] : ''));
    $lowStock     = isset($_POST['low_stock']) && $_POST['low_stock'] !== '' ? max(0, (int)$_POST['low_stock']) : 0;
    $lowStockKantin = isset($_POST['low_stock_kantin']) && $_POST['low_stock_kantin'] !== '' ? max(0, (int)$_POST['low_stock_kantin']) : 0;
    $supplierIds  = normalizeSupplierIds(isset($_POST['supplier_id']) ? $_POST['supplier_id'] : '');

    // Harga 0 rupiah diperbolehkan (produk gratisan); hanya nilai negatif yang ditolak
    if($code === '' || $name === '' || $category === '' || $price < 0){
        echo json_encode(['status'=>'error', 'message'=>'Semua field wajib diisi']);
        exit;
    }

    // Cek duplikasi kode produk
    $dup = mysqli_query($conn, "SELECT id FROM products WHERE code = '$code' AND deleted_at IS NULL LIMIT 1");
    if($dup && mysqli_num_rows($dup) > 0){
        echo json_encode(['status'=>'error', 'message'=>'Kode sudah ada']);
        exit;
    }

    // Upload photo if provided
    $photoName = '';
    if(!empty($_FILES['photo']['name'])){
        $upload = uploadPhoto($_FILES['photo']);
        if(!$upload['ok']){
            echo json_encode(['status'=>'error', 'message'=>$upload['msg']]);
            exit;
        }
        $photoName = $upload['name'];
    }

    // Kategori Additional: tidak pakai satuan, batas stok & supplier → simpan NULL
    $isAdd = strtolower(trim($category)) === 'additional';
    $unitSql   = ($unit === '' || $isAdd) ? 'NULL' : "'$unit'";
    $lowStockSql = $isAdd ? 'NULL' : $lowStock;
    $lowStockKantinSql = $isAdd ? 'NULL' : $lowStockKantin;
    if($isAdd){ $supplierIds = []; }

    $q = mysqli_query($conn,"
        INSERT INTO products (code, name, category, unit, sell_price, low_stock, low_stock_kantin, photo, created_at)
        VALUES ('$code', '$name', '$category', $unitSql, $price, $lowStockSql, $lowStockKantinSql, '$photoName', NOW())
    ");

    if($q){
        $newId = mysqli_insert_id($conn);
        saveProductSuppliers($conn, $newId, $supplierIds);
        echo json_encode(['status'=>'success']);
    }else{
        echo json_encode(['status'=>'error', 'message'=>'Gagal menyimpan data']);
    }
    exit;
}

// UPDATE
if($action === 'update'){

    $id           = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $code         = mysqli_real_escape_string($conn, trim(isset($_POST['code']) ? $_POST['code'] : ''));
    $name         = mysqli_real_escape_string($conn, trim(isset($_POST['name']) ? $_POST['name'] : ''));
    $category     = mysqli_real_escape_string($conn, trim(isset($_POST['category']) ? $_POST['category'] : ''));
    $priceRaw     = isset($_POST['price']) ? $_POST['price'] : '';
    $price        = $priceRaw === '' ? -1 : (int)$priceRaw;
    $unit         = mysqli_real_escape_string($conn, trim(isset($_POST['unit']) ? $_POST['unit'] : ''));
    $lowStock     = isset($_POST['low_stock']) && $_POST['low_stock'] !== '' ? max(0, (int)$_POST['low_stock']) : 0;
    $lowStockKantin = isset($_POST['low_stock_kantin']) && $_POST['low_stock_kantin'] !== '' ? max(0, (int)$_POST['low_stock_kantin']) : 0;
    $oldPhoto     = isset($_POST['old_photo']) ? $_POST['old_photo'] : '';
    $supplierIds  = normalizeSupplierIds(isset($_POST['supplier_id']) ? $_POST['supplier_id'] : '');

    // Harga 0 rupiah diperbolehkan (produk gratisan); hanya nilai negatif yang ditolak
    if($id <= 0 || $code === '' || $name === '' || $category === '' || $price < 0){
        echo json_encode(['status'=>'error', 'message'=>'Semua field wajib diisi']);
        exit;
    }

    // Cek duplikasi kode produk (kecuali milik produk ini sendiri)
    $dup = mysqli_query($conn, "SELECT id FROM products WHERE code = '$code' AND id != $id AND deleted_at IS NULL LIMIT 1");
    if($dup && mysqli_num_rows($dup) > 0){
        echo json_encode(['status'=>'error', 'message'=>'Kode sudah ada']);
        exit;
    }

    // Upload new photo if provided
    $photoName = $oldPhoto;
    if(!empty($_FILES['photo']['name'])){
        $upload = uploadPhoto($_FILES['photo']);
        if(!$upload['ok']){
            echo json_encode(['status'=>'error', 'message'=>$upload['msg']]);
            exit;
        }
        $photoName = $upload['name'];

        // Delete old photo
        if($oldPhoto !== ''){
            $oldPath = '../../assets/img/products/' . $oldPhoto;
            if(file_exists($oldPath)){
                unlink($oldPath);
            }
        }
    }

    // Kategori Additional: simpan unit & low_stock sebagai NULL + kosongkan supplier
    $isAdd = strtolower(trim($category)) === 'additional';
    $unitSql   = ($unit === '' || $isAdd) ? 'NULL' : "'$unit'";
    $lowStockSql = $isAdd ? 'NULL' : $lowStock;
    $lowStockKantinSql = $isAdd ? 'NULL' : $lowStockKantin;
    if($isAdd){ $supplierIds = []; }

    $q = mysqli_query($conn,"
        UPDATE products
        SET code='$code', name='$name', category='$category', sell_price=$price, unit=$unitSql, low_stock=$lowStockSql, low_stock_kantin=$lowStockKantinSql, photo='$photoName'
        WHERE id = $id
    ");

    if($q){
        // Rewrite relasi hanya jika set supplier benar-benar berubah (hindari buang id auto-increment)
        if(!supplierIdsEqual(currentSupplierIds($conn, $id), $supplierIds)){
            saveProductSuppliers($conn, $id, $supplierIds);
        }
        echo json_encode(['status'=>'success']);
    }else{
        echo json_encode(['status'=>'error', 'message'=>'Gagal memperbarui data']);
    }
    exit;
}

// DELETE PHOTO
if($action === 'delete_photo'){

    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    if($id <= 0){
        echo json_encode(['status'=>'error', 'message'=>'ID tidak valid']);
        exit;
    }

    $r = mysqli_query($conn, "SELECT photo FROM products WHERE id = $id");
    $row = $r ? mysqli_fetch_assoc($r) : null;
    $photoName = $row ? basename(trim($row['photo'])) : '';

    $q = mysqli_query($conn, "UPDATE products SET photo = '' WHERE id = $id");

    if(!$q){
        echo json_encode(['status'=>'error', 'message'=>'Gagal menghapus foto']);
        exit;
    }

    if($photoName !== ''){
        $oldPath = '../../assets/img/products/' . $photoName;
        if(file_exists($oldPath)){
            @unlink($oldPath);
        }
    }

    echo json_encode(['status'=>'success']);
    exit;
}

// DESTROY
if($action === 'destroy'){

    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    if($id <= 0){
        echo json_encode(['status'=>'error', 'message'=>'ID tidak valid']);
        exit;
    }

    // Soft delete + bersihkan relasi supplier
    $q = mysqli_query($conn,"
        UPDATE products SET deleted_at = NOW() WHERE id = $id
    ");

    if($q){
        mysqli_query($conn, "DELETE FROM product_supplier WHERE product_id = $id");
        echo json_encode(['status'=>'success']);
    }else{
        echo json_encode(['status'=>'error', 'message'=>'Gagal menghapus data']);
    }
    exit;
}

echo json_encode(['status'=>'error', 'message'=>'Aksi tidak dikenali']);