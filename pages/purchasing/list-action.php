<?php
    include '../../sessions/session.php';
    header('Content-Type: application/json');

    if ($_GET['action'] == 'store') {
        $product_ids = isset($_POST['product_id']) ? $_POST['product_id'] : [];
        $qtys_buy    = isset($_POST['qty_buy']) ? $_POST['qty_buy'] : [];
        $units_buy   = isset($_POST['unit_buy']) ? $_POST['unit_buy'] : [];
        $prices_buy  = isset($_POST['price_buy']) ? $_POST['price_buy'] : [];

        $qLast = mysqli_query($conn,"SELECT MAX(CAST(REPLACE(form, 'FORM-', '') AS UNSIGNED)) AS last_num FROM purchases");
        $dLast = mysqli_fetch_assoc($qLast);
        $nextNum = ($dLast && $dLast['last_num'] ? (int)$dLast['last_num'] : 0) + 1;
        $formNumber = 'FORM-' . str_pad($nextNum, 7, '0', STR_PAD_LEFT);
        $dateNow = date('Y-m-d');

        mysqli_query($conn, "INSERT INTO purchases (form, date) VALUES ('$formNumber', '$dateNow')");
        $purchase_id = mysqli_insert_id($conn);

        for ($i = 0; $i < count($product_ids); $i++) {
            $product_id = (int)$product_ids[$i];
            if ($product_id <= 0) continue;

            $qty_buy   = mysqli_real_escape_string($conn, isset($qtys_buy[$i]) ? $qtys_buy[$i] : 0);
            $unit_buy  = mysqli_real_escape_string($conn, isset($units_buy[$i]) ? $units_buy[$i] : '');
            $price_buy = isset($prices_buy[$i]) && $prices_buy[$i] !== ''
                ? (float)$prices_buy[$i]
                : 0;

            mysqli_query($conn, "INSERT INTO purchase_items
                (purchase_id, product_id, qty_buy, unit_buy, price_buy)
                VALUES
                ($purchase_id, $product_id, $qty_buy, '$unit_buy', $price_buy)");
        }

        echo json_encode([
            "status" => "success",
            "msg" => "Daftar belanja berhasil dibuat"
        ]);
    }

if ($_GET['action'] == 'update') {

        $id = (int)$_POST['id'];

        if ($id <= 0) {
            echo json_encode(['status' => 'error', 'msg' => 'Data tidak valid']);
            exit;
        }

        // item lama milik form ini, urut sesuai id
        $qOld = mysqli_query($conn,"
            SELECT id, qty
            FROM purchase_items
            WHERE purchase_id = '$id'
              AND deleted_at IS NULL
            ORDER BY id ASC
        ");

        $old_items = [];
        while($r = mysqli_fetch_assoc($qOld)){
            $old_items[] = $r;
        }

        // stok sudah dipakai -> daftar belanja tidak boleh diubah
        foreach($old_items as $r){
            if($r['qty'] !== null){
                echo json_encode([
                    'status' => 'error',
                    'msg' => 'Daftar belanja sudah dipakai sebagai stok, tidak dapat diubah'
                ]);
                exit;
            }
        }

        $item_ids     = isset($_POST['item_id']) ? $_POST['item_id'] : [];
        $product_ids  = isset($_POST['product_id']) ? $_POST['product_id'] : [];
        $qtys_buy     = isset($_POST['qty_buy']) ? $_POST['qty_buy'] : [];
        $units_buy    = isset($_POST['unit_buy']) ? $_POST['unit_buy'] : [];
        $prices_buy   = isset($_POST['price_buy']) ? $_POST['price_buy'] : [];

        // id item yang valid & milik form ini
        $valid_ids = [];
        foreach($old_items as $r){
            $valid_ids[(int)$r['id']] = true;
        }

        mysqli_begin_transaction($conn);

        $kept_ids = [];

        foreach($product_ids as $key => $product_id){

            $product_id = (int)$product_id;
            if ($product_id <= 0) continue;

            $item_id = isset($item_ids[$key]) ? (int)$item_ids[$key] : 0;
            if ($item_id > 0 && !isset($valid_ids[$item_id])) $item_id = 0;

            $qty_buy   = isset($qtys_buy[$key]) ? mysqli_real_escape_string($conn, $qtys_buy[$key]) : 0;
            $unit_buy  = isset($units_buy[$key]) ? mysqli_real_escape_string($conn, $units_buy[$key]) : '';
            $price_buy = isset($prices_buy[$key]) && $prices_buy[$key] !== ''
                ? (float)$prices_buy[$key]
                : 0;

            if ($item_id > 0) {
                // update baris yang sama, qty/remaining_qty tidak disentuh
                $run = mysqli_query($conn,"
                    UPDATE purchase_items
                    SET product_id = '$product_id',
                        qty_buy    = '$qty_buy',
                        unit_buy   = '$unit_buy',
                        price_buy  = '$price_buy'
                    WHERE id = '$item_id'
                      AND purchase_id = '$id'
                ");
                $kept_ids[$item_id] = true;
            } else {
                // baris baru
                $run = mysqli_query($conn,"
                    INSERT INTO purchase_items(
                        purchase_id,
                        product_id,
                        qty_buy,
                        unit_buy,
                        price_buy
                    ) VALUES(
                        '$id',
                        '$product_id',
                        '$qty_buy',
                        '$unit_buy',
                        $price_buy
                    )
                ");
            }

            if (!$run) {
                mysqli_rollback($conn);
                echo json_encode([
                    'status' => 'error',
                    'msg' => mysqli_error($conn)
                ]);
                exit;
            }
        }

        // item yang dibuang user: soft delete, hanya yang belum dipakai
        foreach($old_items as $r){
            $oid = (int)$r['id'];
            if (isset($kept_ids[$oid])) continue;

            mysqli_query($conn,"
                UPDATE purchase_items
                SET deleted_at = NOW()
                WHERE id = '$oid'
                  AND purchase_id = '$id'
            ");
        }

        mysqli_commit($conn);

        echo json_encode([
            "status" => "success",
            "msg" => "Daftar belanja berhasil diupdate"
        ]);
        exit;
    }

    if ($_GET['action'] == 'destroy') {
        $id = (int)$_POST['id'];

        // stok sudah dipakai -> daftar belanja tidak boleh dihapus
        $qUsed = mysqli_query($conn,"
            SELECT COUNT(*) AS total
            FROM purchase_items
            WHERE purchase_id = '$id'
              AND deleted_at IS NULL
              AND qty IS NOT NULL
        ");

        $dUsed = mysqli_fetch_assoc($qUsed);

        if ($dUsed && (int)$dUsed['total'] > 0) {
            echo json_encode([
                'status' => 'error',
                'msg' => 'Daftar belanja sudah dipakai sebagai stok, tidak dapat dihapus'
            ]);
            exit;
        }

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
                'status'=>'success',
                "msg" => "Daftar belanja berhasil dihapus"
            ]);
        }else{
            echo json_encode([
                'status'=>'error',
                'msg'=>mysqli_error($conn)
            ]);
        }

        exit;
    }

    if ($_GET['action'] == 'get_print') {

        $id = (int)$_GET['id'];

        $q = mysqli_query($conn,"
            SELECT
                COALESCE(products.name, '') AS name,
                pi.qty_buy AS qty,
                pi.unit_buy AS unit,
                pi.price_buy AS price
            FROM purchase_items pi
            LEFT JOIN products
                ON products.id = pi.product_id
            WHERE pi.purchase_id = '$id'
              AND pi.deleted_at IS NULL
        ");

        $data = [];

        while($d = mysqli_fetch_assoc($q)){
            $data[] = $d;
        }

        echo json_encode([
            "status" => "success",
            "data" => $data
        ]);

        exit;
    }