<?php include '../../sessions/session.php'; ?>

<form id="addUnitForm">
    <div class="section-title">Informasi Unit</div>
    <div class="row">
        <div class="col-md-6">
            <div class="input-group-modern">
                <div class="input-icon"><i class="fas fa-ruler-combined"></i></div>
                <input type="text" name="name" class="form-control" placeholder="Nama Unit (contoh: pcs, karton, slop, dus)" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="input-group-modern">
                <div class="input-icon"><i class="fas fa-sticky-note"></i></div>
                <input type="text" name="note" class="form-control" placeholder="Catatan (opsional)">
            </div>
        </div>
    </div>
    <div class="mp-add-footer">
        <button type="submit" class="btn-save"><i class="fas fa-save"></i> Save</button>
    </div>
</form>
