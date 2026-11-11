<?php
include '../../config/koneksi.php';
include '../../views/header.php';

$id = (int) $_GET['id'];
$get_pakan = mysqli_query($koneksi, "SELECT * FROM pakan WHERE id_pakan = '$id'");
$data = mysqli_fetch_assoc($get_pakan);

if (!$data) {
    echo "<script>alert('Data pakan tidak ditemukan!'); window.location='index.php';</script>";
    exit;
}

if (isset($_POST['update'])) {
    $nama_pakan  = mysqli_real_escape_string($koneksi, $_POST['nama_pakan']);
    $jenis_pakan = mysqli_real_escape_string($koneksi, $_POST['jenis_pakan']);
    $stok_kg     = (float) $_POST['stok_kg'];
    $satuan      = mysqli_real_escape_string($koneksi, $_POST['satuan']);

    $update = mysqli_query($koneksi, "UPDATE pakan SET 
                                        nama_pakan  = '$nama_pakan',
                                        jenis_pakan = '$jenis_pakan',
                                        stok_kg     = '$stok_kg',
                                        satuan      = '$satuan'
                                      WHERE id_pakan = '$id'");

    if ($update) {
        echo "<script>alert('Data pakan berhasil diperbarui!'); window.location='index.php';</script>";
    } else {
        $error = "Gagal memperbarui data: " . mysqli_error($koneksi);
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Edit Data Pakan</h5>
            </div>
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= $error; ?></div>
                <?php endif; ?>

                <form action="" method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Nama Pakan</label>
                            <input type="text" name="nama_pakan" class="form-control" value="<?= htmlspecialchars($data['nama_pakan'] ?? ''); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Jenis Pakan</label>
                            <select name="jenis_pakan" class="form-select" required>
                                <option value="Konsentrat" <?= ($data['jenis_pakan'] ?? '') == 'Konsentrat' ? 'selected' : ''; ?>>Konsentrat</option>
                                <option value="Hijauan" <?= ($data['jenis_pakan'] ?? '') == 'Hijauan' ? 'selected' : ''; ?>>Hijauan</option>
                                <option value="Vitamin" <?= ($data['jenis_pakan'] ?? '') == 'Vitamin' ? 'selected' : ''; ?>>Vitamin</option>
                                <option value="Suplemen" <?= ($data['jenis_pakan'] ?? '') == 'Suplemen' ? 'selected' : ''; ?>>Suplemen</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Stok Saat Ini</label>
                            <input type="number" step="0.01" name="stok_kg" class="form-control" value="<?= $data['stok_kg'] ?? ''; ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Satuan</label>
                            <input type="text" name="satuan" class="form-control" value="<?= htmlspecialchars($data['satuan'] ?? ''); ?>" required>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="index.php" class="btn btn-secondary"><i class="bi bi-arrow-left me-1"></i>Batal</a>
                        <button type="submit" name="update" class="btn btn-warning"><i class="bi bi-save me-1"></i>Update Data Pakan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../../views/footer.php'; ?>