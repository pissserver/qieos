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
