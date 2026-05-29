<?php
    include '../../sessions/session.php';
    header('Content-Type: application/json');

    if ($_GET['action'] === 'stock_in') {

        $formNumber = $_POST['form_number'];
        $name       = $_POST['product_name'];
        $code       = $_POST['code'];
        $category   = $_POST['category'];
        $qty        = (int)$_POST['qty'];
        $unit       = $_POST['unit'];
        $buy_price  = (float)$_POST['buy_price'];
        $sell_price = (float)$_POST['sell_price'];
        $note       = $_POST['note'] ?: '-';
        $date       = date('Y-m-d');

        /* upload */
        $photo_name = null;
        if (!empty($_FILES['photo']['name'])) {
            $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
            $photo_name = 'prod_' . time() . '.' . $ext;
            move_uploaded_file($_FILES['photo']['tmp_name'], "../../assets/img/products/".$photo_name);
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
        mysqli_query($conn, "INSERT INTO purchases (form,date,note) VALUES ('$formNumber','$date','$note')");
        $purchase_id = mysqli_insert_id($conn);

        /* FIFO layer */
        mysqli_query($conn, "INSERT INTO purchase_items
        (purchase_id,product_id,qty,unit,remaining_qty,buy_price,date)
        VALUES ($purchase_id,$product_id,$qty,'$unit',$qty,$buy_price,'$date')");

        echo json_encode([
            "status"=>"success",
            "msg"=>"Stok berhasil ditambahkan"
        ]);
    }

    if($_GET['action']=='next_form'){

        if(!isset($_SESSION['current_form_id'])){
            $_SESSION['current_form_id'] = 1;
        }

        $_SESSION['current_form_id']++;

        echo json_encode([
            'status'=>'success'
        ]);
        exit;
    }

    if($_GET['action']=='prev_form'){

        if(isset($_SESSION['current_form_id']) && $_SESSION['current_form_id'] > 1){
            $_SESSION['current_form_id']--;
        }

        echo json_encode([
            'status'=>'success'
        ]);
        exit;
    }

    if($_GET['action']=='update_purchase_full'){

        $id = $_GET['id'];

        $product_name = $_POST['product_name'];
        $code         = $_POST['code'];
        $category     = $_POST['category'];
        $qty          = $_POST['qty'];
        $unit         = $_POST['unit'];
        $buy_price    = $_POST['buy_price'];
        $sell_price   = $_POST['sell_price'];
        $note         = $_POST['note'];

        /* upload */
        $photo_name = null;
        if (!empty($_FILES['photo']['name'])) {
            $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
            $photo_name = 'prod_' . time() . '.' . $ext;
            move_uploaded_file($_FILES['photo']['tmp_name'], "../../assets/img/products/".$photo_name);
        }

        mysqli_query($conn,"
            UPDATE products 
            SET name='$product_name',
                code='$code',
                category='$category',
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
            SET qty='$qty',
                remaining_qty='$qty',
                unit='$unit',
                buy_price='$buy_price'
            WHERE purchase_id='$id'
        ");

        mysqli_query($conn,"
            UPDATE purchases
            SET note='$note'
            WHERE id='$id'
        ");

        echo json_encode([
            'status'=>'success'
        ]);
        exit;
    }

    if($_GET['action']=='delete_purchase'){

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