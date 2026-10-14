<?php
    include '../../sessions/session.php';
    include __DIR__ . '/../components/data/product-conversion.php';
    header('Content-Type: application/json');

    if ($_GET['action'] == 'store') {
        $product_ids = isset($_POST['product_id']) ? $_POST['product_id'] : [];
        $qtys_buy    = isset($_POST['qty_buy']) ? $_POST['qty_buy'] : [];
        $units_buy   = isset($_POST['unit_buy']) ? $_POST['unit_buy'] : [];
        $unit_prices = isset($_POST['unit_price']) ? $_POST['unit_price'] : [];
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
            $unit_price = isset($unit_prices[$i]) && $unit_prices[$i] !== ''
                ? (float)$unit_prices[$i]
                : null;
            $price_buy = isset($prices_buy[$i]) && $prices_buy[$i] !== ''
                ? (float)$prices_buy[$i]
                : 0;

            $unit_price_sql = $unit_price === null ? 'NULL' : $unit_price;

            mysqli_query($conn, "INSERT INTO purchase_items
                (purchase_id, product_id, qty_buy, unit_buy, price_unit, price_buy)
                VALUES
                ($purchase_id, $product_id, $qty_buy, '$unit_buy', $unit_price_sql, $price_buy)");
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
            SELECT id, qty_stock
            FROM purchase_items
            WHERE purchase_id = '$id'
              AND deleted_at IS NULL
            ORDER BY id ASC
        ");

        if(!$qOld){
            echo json_encode(['status' => 'error', 'msg' => 'Gagal membaca data: ' . mysqli_error($conn)]);
            exit;
        }

        $old_items = [];
        while($r = mysqli_fetch_assoc($qOld)){
            $old_items[] = $r;
        }

        // stok sudah dipakai -> daftar belanja tidak boleh diubah
        foreach($old_items as $r){
            if($r['qty_stock'] !== null){
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
        $unit_prices  = isset($_POST['unit_price']) ? $_POST['unit_price'] : [];
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
            $unit_price = isset($unit_prices[$key]) && $unit_prices[$key] !== ''
                ? (float)$unit_prices[$key]
                : null;
            $price_buy = isset($prices_buy[$key]) && $prices_buy[$key] !== ''
                ? (float)$prices_buy[$key]
                : 0;

            $unit_price_sql = $unit_price === null ? 'NULL' : $unit_price;

            if ($item_id > 0) {
                // update baris yang sama, qty/remaining_qty tidak disentuh
                $run = mysqli_query($conn,"
                    UPDATE purchase_items
                    SET product_id = '$product_id',
                        qty_buy    = '$qty_buy',
                        unit_buy   = '$unit_buy',
                        price_unit = $unit_price_sql,
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
                        price_unit,
                        price_buy
                    ) VALUES(
                        '$id',
                        '$product_id',
                        '$qty_buy',
                        '$unit_buy',
                        $unit_price_sql,
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
              AND qty_stock IS NOT NULL
        ");

        if(!$qUsed){
            echo json_encode(['status' => 'error', 'msg' => 'Gagal membaca data: ' . mysqli_error($conn)]);
            exit;
        }

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

    // Validasi & Restock: daftar belanja dijadikan stok gudang (FIFO layer)
    if ($_GET['action'] == 'validate') {

        $id = (int)$_POST['id'];

        if ($id <= 0) {
            echo json_encode(['status' => 'error', 'msg' => 'Data tidak valid']);
            exit;
        }

        $qCek = mysqli_query($conn,"
            SELECT COUNT(*) AS total
            FROM purchases
            WHERE id = '$id'
              AND deleted_at IS NULL
        ");

        $dCek = $qCek ? mysqli_fetch_assoc($qCek) : null;
        if (!$dCek || (int)$dCek['total'] === 0) {
            echo json_encode(['status' => 'error', 'msg' => 'Daftar belanja tidak ditemukan']);
            exit;
        }

        $qPending = mysqli_query($conn,"
            SELECT id
            FROM purchase_items
            WHERE purchase_id = '$id'
              AND deleted_at IS NULL
              AND qty_buy IS NOT NULL
              AND qty_stock IS NULL
            ORDER BY id ASC
        ");

        $pending = [];
        while ($qPending && $r = mysqli_fetch_assoc($qPending)) {
            $pending[] = (int)$r['id'];
        }

        if (!$pending) {
            echo json_encode(['status' => 'error', 'msg' => 'Daftar belanja sudah diinput / tidak ada item untuk direstock']);
            exit;
        }

        // Guard: satuan beli berbeda dari satuan produk wajib punya konversi.
        foreach ($pending as $item_id) {
            $qchk = mysqli_query($conn,"
                SELECT product_id, unit_buy
                FROM purchase_items
                WHERE id = '$item_id'
                  AND purchase_id = '$id'
                  AND deleted_at IS NULL
                LIMIT 1
            ");
            $ichk = $qchk ? mysqli_fetch_assoc($qchk) : null;
            if (!$ichk) continue;

            $ubChk = isset($ichk['unit_buy']) ? trim($ichk['unit_buy']) : '';
            $cchk  = product_conversion_for_unit($conn, (int)$ichk['product_id'], $ubChk);

            if (!$cchk['has_convert'] && strtolower($ubChk) !== strtolower($cchk['unit'])) {
                echo json_encode([
                    'status' => 'error',
                    'msg'    => 'Tidak bisa restock: satuan beli "' . $ubChk
                        . '" tidak sama dengan satuan produk dan belum ada konversi'
                ]);
                exit;
            }
        }

        mysqli_begin_transaction($conn);

        $done = 0;

        foreach ($pending as $item_id) {

            $qi = mysqli_query($conn,"
                SELECT product_id, qty_buy, unit_buy
                FROM purchase_items
                WHERE id = '$item_id'
                  AND purchase_id = '$id'
                  AND deleted_at IS NULL
                LIMIT 1
            ");

            $it = $qi ? mysqli_fetch_assoc($qi) : null;

            if (!$it) {
                mysqli_rollback($conn);
                echo json_encode(['status' => 'error', 'msg' => 'Gagal membaca item daftar belanja']);
                exit;
            }

            $product_id = (int)$it['product_id'];
            $qty_buy    = (float)$it['qty_buy'];
            $unit_buy   = isset($it['unit_buy']) ? $it['unit_buy'] : '';

            // Bila produk punya konversi yang cocok dengan satuan beli,
            // qty belanja dikonversi ke satuan stok. Jika tidak, qty belanja
            // langsung dipakai sebagai stok.
            $conv = product_conversion_for_unit($conn, $product_id, $unit_buy);

            if ($conv['has_convert'] && (float)$conv['factor'] > 0) {
                $qtyStock  = (int)round($qty_buy * (float)$conv['factor']);
                $unitStock = $conv['base_unit'] !== '' ? $conv['base_unit'] : $conv['unit'];
            } else {
                $qtyStock  = (int)round($qty_buy);
                $unitStock = $conv['unit'] !== '' ? $conv['unit'] : $unit_buy;
            }

            $unitStockEsc = mysqli_real_escape_string($conn, $unitStock);

            $run = mysqli_query($conn,"
                UPDATE purchase_items
                SET qty_stock     = $qtyStock,
                    unit_stock    = '$unitStockEsc',
                    remaining_qty = $qtyStock
                WHERE id = $item_id
                  AND purchase_id = '$id'
                  AND deleted_at IS NULL
            ");

            if (!$run) {
                mysqli_rollback($conn);
                echo json_encode(['status' => 'error', 'msg' => mysqli_error($conn)]);
                exit;
            }

            $done++;
        }

        mysqli_commit($conn);

        echo json_encode([
            'status' => 'success',
            'msg'    => "$done item berhasil direstock ke stok gudang"
        ]);
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