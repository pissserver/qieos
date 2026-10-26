<?php include '../../sessions/session.php'; ?>

<form id="addSupplierForm">
    <div class="section-title">Informasi Supplier</div>
    <div class="row">
        <div class="col-md-6">
            <div class="input-group-modern">
                <div class="input-icon"><i class="fas fa-truck"></i></div>
                <input type="text" name="name" class="form-control" placeholder="Nama Supplier" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="input-group-modern">
                <div class="input-icon"><i class="fas fa-phone"></i></div>
                <input type="text" name="phone" class="form-control" placeholder="No. Telepon">
            </div>
        </div>
        <div class="col-md-12">
            <div class="input-group-modern">
                <div class="input-icon"><i class="fas fa-map-marker-alt"></i></div>
                <textarea name="address" class="form-control" placeholder="Alamat Lengkap"></textarea>
            </div>
        </div>
        <div class="col-md-12">
            <div class="input-group-modern">
                <div class="input-icon"><i class="fas fa-sticky-note"></i></div>
                <textarea name="note" class="form-control" placeholder="Catatan"></textarea>
            </div>
        </div>
    </div>
    <div class="mp-add-footer">
        <button type="submit" class="btn-save"><i class="fas fa-save"></i> Save</button>
    </div>
</form>