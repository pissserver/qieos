<?php

    include '../sessions/session.php';

    // Action add
    if(isset($_GET['action']) && $_GET['action'] == 'add'){
        if(isset($_POST['save_product'])){

            $category = $_POST['category'];
            $product_name = $_POST['product_name'];
            $price = $_POST['price'];

            $photo = $_FILES['photo']['name'];
            $tmp = $_FILES['photo']['tmp_name'];

            $created_at = date("Y-m-d");

            $folder = "../assets/img/uploads/";

            move_uploaded_file($tmp,$folder.$photo);

            $query = mysqli_query($conn,"INSERT INTO products (category,product_name,price,photo,created_at)
                                    VALUES ('$category','$product_name','$price','$photo','$created_at')");

            if($query){
                header("Location: product-add.php?status=success&msg=Produk berhasil ditambahkan");
                exit();
            }else{
                header("Location: product-add.php?status=error&msg=Produk gagal ditambahkan");
                exit();
            }
        }
    }

    // Action edit
    if(isset($_GET['action']) && $_GET['action'] == 'edit'){
        if(isset($_POST['edit_product'])){

            $id = $_GET['id'];
            $category = $_POST['category'];
            $product_name = $_POST['product_name'];
            $price = $_POST['price'];

            $photo = $_FILES['photo']['name'];
            $tmp = $_FILES['photo']['tmp_name'];

            $folder = "../assets/img/uploads/";

            $get = mysqli_query($conn,"SELECT photo FROM products WHERE id='$id'");
            $data = mysqli_fetch_assoc($get);
            $old_photo = $data['photo'];

            if($photo){
                if($old_photo && file_exists($folder.$old_photo)){
                    unlink($folder.$old_photo);
                }

                move_uploaded_file($tmp,$folder.$photo);
                $query = mysqli_query($conn,"UPDATE products SET category='$category', product_name='$product_name', price='$price', photo='$photo' WHERE id='$id'");
            }else{
                $query = mysqli_query($conn,"UPDATE products SET category='$category', product_name='$product_name', price='$price' WHERE id='$id'");
            }

            if($query){
                header("Location: product-add.php?status=success&msg=Produk berhasil diupdate");
                exit();
            }else{
                header("Location: product-add.php?status=error&msg=Produk gagal diupdate");
                exit();
            }
        }
    }

    // Action delete
    if(isset($_GET['action']) && $_GET['action'] == 'delete'){
        if(isset($_GET['id'])){

            $id = $_GET['id'];

            
            $folder = "../assets/img/uploads/";
            
            $get = mysqli_query($conn,"SELECT photo FROM products WHERE id='$id'");
            $data = mysqli_fetch_assoc($get);
            $old_photo = $data['photo'];
            
            if($old_photo && file_exists($folder.$old_photo)){
                unlink($folder.$old_photo);
            }

            $query = mysqli_query($conn,"DELETE FROM products WHERE id='$id'");

            if($query){
                header("Location: product-add.php?status=success&msg=Produk berhasil dihapus");
                exit();
            }else{
                header("Location: product-add.php?status=error&msg=Produk gagal dihapus");
                exit();
            }
        }
    }