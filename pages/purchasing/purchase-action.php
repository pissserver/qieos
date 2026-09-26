<?php
    include '../../sessions/session.php';
    header('Content-Type: application/json');

    // buat pembelian baru
    if ($_GET['action'] === 'store') {

        $formNumber = $_POST['form_number'];
        $name       = $_POST['product_name'];
        $code       = $_POST['code'];
        $category   = $_POST['category'];
        $qty        = (int)$_POST['qty'];
        $unit       = $_POST['unit'];
        $buy_price  = (float)$_POST['buy_price'];
        $sell_price = (float)$_POST['sell_price'];
        $date       = date('Y-m-d');

        /* cek produk & ambil foto lama */
        $cek = mysqli_query($conn, "SELECT * FROM products WHERE code='$code' LIMIT 1");
        $p = mysqli_fetch_assoc($cek);

        // default pakai foto lama
        $photo_name = $p['photo'] ? $p['photo'] : null;

        /* upload foto baru jika ada */
        if (!empty($_FILES['photo']['name'])) {
            $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
            $photo_name = 'prod_' . time() . '.' . $ext;

            move_uploaded_file(
                $_FILES['photo']['tmp_name'],
                "../../assets/img/products/".$photo_name
            );
        }
    

        /* cek produk */
        $cek = mysqli_query($conn, "SELECT * FROM products WHERE code='$code' LIMIT 1");
        if(mysqli_num_rows($cek)){
            $p = mysqli_fetch_assoc($cek);
            $product_id = $p['id'];
            mysqli_query($conn, "UPDATE products SET sell_price='$sell_price', photo='$photo_name' WHERE id=$product_id");
        } else {
            mysqli_query($conn, "INSERT INTO products (name,code,category,sell_price,photo)
            VALUES ('$name','$code','$category','$sell_price','$photo_name')");
            $product_id = mysqli_insert_id($conn);
        }

        /* header */
        mysqli_query($conn, "INSERT INTO purchases (form,date) VALUES ('$formNumber','$date')");
        $purchase_id = mysqli_insert_id($conn);

        /* FIFO layer */
        mysqli_query($conn, "INSERT INTO purchase_items
        (purchase_id,product_id,qty,unit,remaining_qty,price)
        VALUES ($purchase_id,$product_id,$qty,'$unit',$qty,$buy_price)");

        echo json_encode([
            "status"=>"success",
            "msg"=>"Stok berhasil ditambahkan"
        ]);
    }

    // simpan qty & harga beli dari daftar belanja (convert ke pembelian)
    if ($_GET['action'] === 'save_items') {

        $purchase_id = (int)$_POST['purchase_id'];
        $item_ids    = isset($_POST['item_id']) ? $_POST['item_id'] : [];
        $qtys        = isset($_POST['qty']) ? $_POST['qty'] : [];
        $prices      = isset($_POST['price']) ? $_POST['price'] : [];

        $cek = mysqli_query($conn, "SELECT id FROM purchases WHERE id='$purchase_id' AND deleted_at IS NULL");
        if (mysqli_num_rows($cek) == 0) {
            echo json_encode([
                "status" => "error",
                "msg" => "Form pembelian tidak ditemukan"
            ]);
            exit;
        }

        $saved = 0;

        foreach ($item_ids as $key => $item_id) {
            $item_id = (int)$item_id;

            if (!isset($qtys[$key]) || !isset($prices[$key])) continue;
            if ($qtys[$key] === '' || $prices[$key] === '') continue;

            $qty   = (int)$qtys[$key];
            $price = (float)$prices[$key];

            if ($qty <= 0) continue;

            $update = mysqli_query($conn,"
                UPDATE purchase_items pi
                JOIN products pr ON pr.id = pi.product_id
                SET pi.qty = '$qty',
                    pi.unit = pr.unit,
                    pi.remaining_qty = '$qty',
                    pi.price = '$price'
                WHERE pi.id = '$item_id'
                  AND pi.purchase_id = '$purchase_id'
                  AND pi.deleted_at IS NULL
            ");

            if ($update) $saved++;
        }

        echo json_encode([
            "status" => "success",
            "msg" => "$saved item pembelian berhasil disimpan",
            "form_id" => $purchase_id
        ]);
        exit;
    }

    // form navigation next
    if($_GET['action']=='next_form'){

        if(!isset($_SESSION['current_form_id'])){
            $_SESSION['current_form_id'] = 1;
        }

        $_SESSION['current_form_id']++;

        $formNumber = 'FORM-' . str_pad($_SESSION['current_form_id'], 7, '0', STR_PAD_LEFT);

        echo json_encode([
            'status'=>'success',
            'form_number'=>$formNumber,
            'form_id'=>$_SESSION['current_form_id']
        ]);
        exit;
    }

    // form navigation prev
    if($_GET['action']=='prev_form'){

        if(isset($_SESSION['current_form_id']) && $_SESSION['current_form_id'] > 1){
            $_SESSION['current_form_id']--;
        }

        $formNumber = 'FORM-' . str_pad($_SESSION['current_form_id'], 7, '0', STR_PAD_LEFT);

        echo json_encode([
            'status'=>'success',
            'form_number'=>$formNumber,
            'form_id'=>$_SESSION['current_form_id']
        ]);
        exit;
    }

    // update pembelian
    if($_GET['action']=='update'){

        $id = $_GET['id'];

        $product_name = $_POST['product_name'];
        $code         = $_POST['code'];
        $category     = $_POST['category'];
        $qty          = $_POST['qty'];
        $unit         = $_POST['unit'];
        $buy_price    = $_POST['buy_price'];
        $sell_price   = $_POST['sell_price'];

        /* cek produk & ambil foto lama */
        $cek = mysqli_query($conn, "SELECT * FROM products WHERE code='$code' LIMIT 1");
        $p = mysqli_fetch_assoc($cek);

        // default pakai foto lama
        $photo_name = $p['photo'] ? $p['photo'] : null;

        /* upload foto baru jika ada */
        if (!empty($_FILES['photo']['name'])) {
            $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
            $photo_name = 'prod_' . time() . '.' . $ext;

            move_uploaded_file(
                $_FILES['photo']['tmp_name'],
                "../../assets/img/products/".$photo_name
            );
        }

        if($p){

            mysqli_query($conn,"
                UPDATE products 
                SET category='$category',
                    sell_price='$sell_price',
                    photo='$photo_name'
                WHERE id = (
                    SELECT product_id 
                    FROM purchase_items 
                    WHERE purchase_id='$id'
                )
            ");

            mysqli_query($conn,"
                UPDATE purchase_items
                SET product_id='{$p['id']}',
                    qty='$qty',
                    remaining_qty='$qty',
                    unit='$unit',
                    price='$buy_price'
                WHERE purchase_id='$id'
            ");
        } else {

            mysqli_query($conn, "
                INSERT INTO products (name,code,category,sell_price,photo)
                VALUES ('$product_name','$code','$category','$sell_price','$photo_name')
            ");

            $product_id = mysqli_insert_id($conn);

            mysqli_query($conn,"
                UPDATE purchase_items
                SET product_id='$product_id',
                    qty='$qty',
                    remaining_qty='$qty',
                    unit='$unit',
                    price='$buy_price'
                WHERE purchase_id='$id'
            ");
        }

        echo json_encode([
            'status'=>'success'
        ]);
        exit;
    }

    // hapus pembelian
    if($_GET['action']=='destroy'){

        $id = (int)$_GET['id'];

        $u1 = mysqli_query($conn,"
            UPDATE purchases
            SET deleted_at = NOW()
            WHERE id='$id'
        ");

        $u2 = mysqli_query($conn,"
            UPDATE purchase_items
            SET deleted_at = NOW()
            WHERE purchase_id='$id'
        ");

        if($u1 && $u2){

            echo json_encode([
                'status'=>'success'
            ]);

        }else{

            echo json_encode([
                'status'=>'error',
                'msg'=>mysqli_error($conn)
            ]);

        }

        exit;
    }