<?php
    include '../../sessions/session.php';
    header('Content-Type: application/json');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = isset($_GET['action']) ? $_GET['action'] : null;

        if ($action === 'stock_in') {
            $name       = mysqli_real_escape_string($conn, $_POST['product_name']);
            $code       = mysqli_real_escape_string($conn, $_POST['code']);
            $category   = mysqli_real_escape_string($conn, $_POST['category']);
            $qty        = (int)$_POST['qty'];
            $unit       = mysqli_real_escape_string($conn, $_POST['unit']);
            $buy_price  = (float)$_POST['buy_price'];
            $sell_price = (float)$_POST['sell_price'];
            $note       = mysqli_real_escape_string($conn, $_POST['note'] ? $_POST['note'] : '-');
            $date       = date('Y-m-d');

            /* ===== Upload gambar (optional) ===== */
            $photo_name = null;

            if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {

                $target_dir = "../../assets/img/products/";

                // pastikan folder ada
                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }

                $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
                $photo_name = 'prod_' . time() . '.' . $ext;

                $target_file = $target_dir . $photo_name;

                if (!move_uploaded_file($_FILES['photo']['tmp_name'], $target_file)) {
                    echo json_encode([
                        "status" => "error",
                        "msg" => "Gagal upload gambar"
                    ]);
                    exit;
                }
            }

            /* ===== 1. Cek / buat produk ===== */
            $cek = mysqli_query($conn, "SELECT * FROM products WHERE name='$name' LIMIT 1");
            if (mysqli_num_rows($cek) > 0) {
                $p = mysqli_fetch_assoc($cek);
                $product_id = $p['id'];

                // update harga jual terbaru (opsional)
                mysqli_query($conn, "UPDATE products SET sell_price='$sell_price' WHERE id=$product_id");

            } else {
                mysqli_query($conn, "
                    INSERT INTO products (name, code, category, sell_price, photo)
                    VALUES ('$name', '$code', '$category', '$sell_price', '$photo_name')
                ");
                $product_id = mysqli_insert_id($conn);
            }

            /* ===== 2. Insert header pembelian ===== */
            mysqli_query($conn, "
                INSERT INTO purchases (date, note)
                VALUES ('$date', '$note')
            ");
            $purchase_id = mysqli_insert_id($conn);

            /* ===== 3. Insert batch FIFO ===== */
            mysqli_query($conn, "
                INSERT INTO purchase_items
                (purchase_id, product_id, qty, unit, remaining_qty, buy_price, date)
                VALUES
                ($purchase_id, $product_id, $qty, '$unit', $qty, $buy_price, '$date')
            ");

            echo json_encode([
                "status" => "success",
                "msg" => "Stok masuk berhasil"
            ]);
            exit;
        }
    }