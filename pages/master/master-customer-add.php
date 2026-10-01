<?php include '../../sessions/session.php'; ?>
<form id="addCustomerForm">
    <div class="section-title">Informasi Customer</div>
    <div class="row">
        <div class="col-md-6">
            <div class="input-group-modern">
                <div class="input-icon"><i class="fas fa-user"></i></div>
                <input type="text" name="name" class="form-control" placeholder="Nama Customer" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="input-group-modern">
                <div class="input-icon"><i class="fas fa-phone"></i></div>
                <input type="text" name="phone" class="form-control" placeholder="No. Telepon">
            </div>
        </div>
    </div>
    <div class="mp-add-footer">
        <button type="submit" class="btn-save"><i class="fas fa-save"></i> Save</button>
    </div>
</form>