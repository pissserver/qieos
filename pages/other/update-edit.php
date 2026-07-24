<?php
include '../../sessions/session.php';

$id = (int)$_GET['id'];

// Data update
$q = mysqli_query($conn, "
    SELECT *
    FROM updates
    WHERE id = '$id'
");

if (!$q) {
    die(mysqli_error($conn));
}

$d = mysqli_fetch_assoc($q);

// Detail update
$qDetail = mysqli_query($conn, "
    SELECT *
    FROM update_details
    WHERE update_id = '$id'
    ORDER BY id ASC
");
?>

<form id="editUpdateForm">

    <input type="hidden" name="id" value="<?= $id ?>">

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
                    value="<?= htmlspecialchars($d['update_name']) ?>"
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
                    value="<?= htmlspecialchars($d['update_version']) ?>"
                    required>
            </div>
        </div>

        <!-- Jenis -->
        <div class="col-md-3">
            <div class="input-group-modern">
                <div class="input-icon">
                    <i class="fas fa-layer-group"></i>
                </div>

                <select
                    name="update_type"
                    class="form-select"
                    required>

                    <option value="major" <?= $d['update_type']=='major' ? 'selected' : '' ?>>
                        Major
                    </option>

                    <option value="minor" <?= $d['update_type']=='minor' ? 'selected' : '' ?>>
                        Minor
                    </option>

                    <option value="patch" <?= $d['update_type']=='patch' ? 'selected' : '' ?>>
                        Patch
                    </option>

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
                    value="<?= date('Y-m-d\TH:i', strtotime($d['update_date'])) ?>"
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
            id="addDescriptionEdit">

            <i class="fas fa-plus"></i>
            Tambah Deskripsi

        </button>

    </div>

    <div id="descriptionContainer">

        <?php while($detail = mysqli_fetch_assoc($qDetail)) { ?>

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
                            value="<?= htmlspecialchars($detail['description']) ?>"
                            required>

                    </div>

                </div>

                <div class="col-md-1">

                    <button
                        type="button"
                        class="btn btn-danger w-100 removeDescriptionEdit">

                        <i class="fas fa-trash"></i>

                    </button>

                </div>

            </div>

        <?php } ?>

    </div>

    <div class="text-end mt-4 mb-4">

        <button type="submit" class="btn-save">

            <i class="fas fa-save me-1"></i>
            Update

        </button>

    </div>

</form>