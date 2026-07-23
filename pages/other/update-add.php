<?php
include '../../sessions/session.php';
?>

<form id="addUpdateForm">

    <div class="section-title">
        Informasi Update
    </div>

    <div class="row">

        <!-- Nama Update -->
        <div class="col-md-6">
            <div class="input-group-modern">
                <div class="input-icon">
                    <i class="fas fa-tag"></i>
                </div>
                <input
                    type="text"
                    name="update_name"
                    class="form-control"
                    placeholder="Contoh : Qieos POS"
                    required>
            </div>
        </div>

        <!-- Versi -->
        <div class="col-md-3">
            <div class="input-group-modern">
                <div class="input-icon">
                    <i class="fas fa-code-branch"></i>
                </div>
                <input
                    type="text"
                    name="update_version"
                    class="form-control"
                    placeholder="v1.2.0"
                    required>
            </div>
        </div>

        <!-- Type -->
        <div class="col-md-3">
            <div class="input-group-modern">
                <div class="input-icon">
                    <i class="fas fa-layer-group"></i>
                </div>

                <select
                    name="update_type"
                    class="form-select"
                    required>

                    <option value="">Jenis Update</option>
                    <option value="major">Major</option>
                    <option value="minor">Minor</option>
                    <option value="patch">Patch</option>

                </select>

            </div>
        </div>

        <!-- Tanggal -->
        <div class="col-md-12 mt-3">
            <div class="input-group-modern">
                <div class="input-icon">
                    <i class="fas fa-calendar"></i>
                </div>
                <input
                    type="datetime-local"
                    name="update_date"
                    class="form-control"
                    value="<?= date('Y-m-d\TH:i') ?>"
                    required>
            </div>
        </div>

    </div>

    <hr class="my-4">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <div class="section-title mb-0">
            Detail Update
        </div>

        <button
            type="button"
            class="btn btn-primary btn-sm"
            id="addDescription">

            <i class="fas fa-plus"></i>
            Tambah Deskripsi

        </button>

    </div>

    <div id="descriptionContainer">

        <div class="row description-row mb-3">

            <div class="col-md-11">

                <div class="input-group-modern">

                    <div class="input-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>

                    <input
                        type="text"
                        name="description[]"
                        class="form-control"
                        placeholder="Contoh : Menambahkan fitur laporan pembayaran tenant"
                        required>

                </div>

            </div>

            <div class="col-md-1">

                <button
                    type="button"
                    class="btn btn-danger w-100 removeDescription">

                    <i class="fas fa-trash"></i>

                </button>

            </div>

        </div>

    </div>

    <div class="text-end mt-4 mb-4">

        <button type="submit" class="btn-save">

            <i class="fas fa-save me-1"></i>
            Simpan Update

        </button>

    </div>

</form>