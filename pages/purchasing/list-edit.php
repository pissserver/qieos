<?php
include '../../sessions/session.php';

$id = (int)$_GET['id'];

$q = mysqli_query($conn,"
    SELECT
        li.id AS item_id,
        li.name,
        li.qty,
        li.unit
    FROM list_purchases lp
    LEFT JOIN list_purchase_items li
        ON li.list_purchase_id = lp.id
    WHERE lp.id = '$id'
    ORDER BY li.id ASC
");

if(!$q){
    die(mysqli_error($conn));
}
?>

<form id="editPurchaseForm" method="POST">

    <input type="hidden" name="id" value="<?= $id ?>">

    <div id="itemsContainerEdit">

        <?php while($d = mysqli_fetch_assoc($q)){ ?>

        <div class="item-row row mb-3">

            <input type="hidden"
                   name="item_id[]"
                   value="<?= $d['item_id'] ?>">

            <div class="col-md-4">
                <input type="text"
                       name="product_name[]"
                       class="form-control"
                       value="<?= htmlspecialchars($d['name']) ?>"
                       required>
            </div>

            <div class="col-md-3">
                <input type="number"
                       name="qty[]"
                       class="form-control"
                       value="<?= $d['qty'] ?>"
                       required>
            </div>

            <div class="col-md-3">
                <input type="text"
                       name="unit[]"
                       class="form-control"
                       value="<?= htmlspecialchars($d['unit']) ?>"
                       required>
            </div>

            <div class="col-md-2">
                <button type="button"
                        class="btn btn-danger w-100"
                        onclick="removeItem(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </div>

        </div>

        <?php } ?>

    </div>

    <div class="d-flex justify-content-end gap-2 mt-4 mb-4">

        <button type="button"
                class="btn-save"
                onclick="addItemEdit()">
            <i class="fas fa-plus"></i>
            Tambah Item
        </button>

        <button type="submit"
                class="btn-save">
            <i class="fas fa-save"></i>
            Update
        </button>

    </div>

</form>

<script>
    function removeItem(button)
    {
        button.closest('.item-row').remove();
    }
</script>