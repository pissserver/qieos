<?php
/**
 * Helper konversi unit untuk sebuah produk.
 *
 * product_matched_conversions($conn, $productId)
 *   Mengembalikan:
 *     unit          : satuan produk (string)
 *     conversion_id : id konversi yang sedang dipakai (0 bila belum / tidak cocok)
 *     matched       : daftar konversi aktif yang base_unit-nya sama dengan
 *                     satuan produk, masing-masing berisi items[]
 *                     (unit_name, qty)
 *
 * Karena satu unit base hanya boleh dipakai sekali, idealnya matched
 * maksimal 1 entri. Tetap ditulis generik supaya aman.
 */
if(!function_exists('product_matched_conversions')){
    function product_matched_conversions($conn, $productId){
        $out = ['unit' => '', 'conversion_id' => 0, 'matched' => []];
        $productId = (int)$productId;
        if($productId <= 0) return $out;

        $rp = mysqli_query($conn, "SELECT unit, category, conversion_id FROM products WHERE id = $productId AND deleted_at IS NULL LIMIT 1");
        $p = $rp ? mysqli_fetch_assoc($rp) : null;
        if(!$p) return $out;

        $unit  = isset($p['unit']) ? trim($p['unit']) : '';
        $isAdd = strtolower(trim(isset($p['category']) ? $p['category'] : '')) === 'additional';
        $out['unit'] = $unit;
        $out['conversion_id'] = !empty($p['conversion_id']) ? (int)$p['conversion_id'] : 0;

        if($unit === '' || $isAdd){
            $out['conversion_id'] = 0;
            return $out;
        }

        $unitEsc = mysqli_real_escape_string($conn, $unit);
        $matched = [];
        $idx     = [];
        $q = mysqli_query($conn, "
            SELECT id, base_unit
            FROM unit_conversions
            WHERE deleted_at IS NULL AND LOWER(base_unit) = LOWER('$unitEsc')
            ORDER BY id ASC
        ");
        if($q){
            while($row = mysqli_fetch_assoc($q)){
                $matched[] = ['id' => (int)$row['id'], 'base_unit' => $row['base_unit'], 'items' => []];
                $idx[(int)$row['id']] = count($matched) - 1;
            }
        }

        if(!empty($matched)){
            $idsStr = implode(',', array_keys($idx));
            $qi = mysqli_query($conn, "
                SELECT conversion_id, unit_name, qty
                FROM unit_conversion_items
                WHERE conversion_id IN ($idsStr)
                ORDER BY id ASC
            ");
            if($qi){
                while($it = mysqli_fetch_assoc($qi)){
                    $cid = (int)$it['conversion_id'];
                    if(isset($idx[$cid])){
                        $matched[$idx[$cid]]['items'][] = ['unit_name' => $it['unit_name'], 'qty' => (float)$it['qty']];
                    }
                }
            }
        }
        $out['matched'] = $matched;

        // Pastikan konversi yang tersimpan masih ada di daftar yang cocok
        $valid = false;
        foreach($matched as $m){
            if($m['id'] === $out['conversion_id']){ $valid = true; break; }
        }
        if(!$valid) $out['conversion_id'] = 0;

        return $out;
    }
}

/**
 * Konversi yang berlaku untuk satu baris belanja.
 *
 * product_conversion_for_unit($conn, $productId, $unitBuy)
 *   Mengembalikan:
 *     unit          : satuan produk (satuan stok / base)
 *     base_unit     : satuan stok (base konversi)
 *     unit_buy      : satuan beli yang dipakai
 *     conversion_id : id konversi terpasang (0 bila belum)
 *     factor        : qty base untuk 1 unit_buy (0 bila tak bisa dikonversi)
 *     has_convert   : true bila unit_buy bisa dikonversi ke base
 *
 * Konversi dianggap "ada" hanya bila produk punya konversi terpasang
 * (conversion_id) dan salah satu item-nya cocok dengan unit_buy.
 */
if(!function_exists('product_conversion_for_unit')){
    function product_conversion_for_unit($conn, $productId, $unitBuy){
        $out = [
            'unit'          => '',
            'base_unit'     => '',
            'unit_buy'      => is_string($unitBuy) ? trim($unitBuy) : '',
            'conversion_id' => 0,
            'factor'        => 0,
            'has_convert'   => false,
        ];

        if(!function_exists('product_matched_conversions')){
            return $out;
        }

        $info = product_matched_conversions($conn, $productId);

        $out['unit']          = $info['unit'];
        $out['base_unit']     = $info['unit'];
        $out['conversion_id'] = $info['conversion_id'];

        if($info['conversion_id'] <= 0) return $out;

        $ub = strtolower($out['unit_buy']);
        if($ub === '') return $out;

        foreach($info['matched'] as $m){
            if((int)$m['id'] !== (int)$info['conversion_id']) continue;

            foreach($m['items'] as $it){
                if(strtolower(trim($it['unit_name'])) === $ub && (float)$it['qty'] > 0){
                    $out['factor']      = (float)$it['qty'];
                    $out['has_convert'] = true;
                    break 2;
                }
            }
        }

        return $out;
    }
}

/**
 * Label qty rapi: bilangan bulat tanpa desimal, selain itu tanpa nol
 * di belakang koma. Contoh: 100 -> "100", 1.5 -> "1,5".
 */
if(!function_exists('product_qty_label')){
    function product_qty_label($n){
        $n = (float)$n;
        if(floor($n) == $n){
            return number_format($n, 0, ',', '.');
        }
        return rtrim(rtrim(number_format($n, 4, ',', '.'), '0'), ',');
    }
}
