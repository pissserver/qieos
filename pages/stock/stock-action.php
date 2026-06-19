<?php
    include '../../sessions/session.php';
    header('Content-Type: application/json');

    if($_GET['action']=='update'){

        $id = $_POST['id'];

        $product_name = $_POST['product_name'];
        $sell_price   = $_POST['sell_price'];

        /* cek produk & ambil foto lama */
        $cek = mysqli_query($conn, "SELECT * FROM products WHERE id='$id' LIMIT 1");
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
                SET name='$product_name',
                    sell_price='$sell_price',
                    photo='$photo_name'
                WHERE id = '$id'
            ");

        }

        echo json_encode([
            'status'=>'success'
        ]);
        exit;
    }