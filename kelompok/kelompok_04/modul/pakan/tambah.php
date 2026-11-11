<?php
include '../../config/koneksi.php';
include '../../views/header.php';

if (isset($_POST['simpan'])) {
    $nama_pakan  = mysqli_real_escape_string($koneksi, $_POST['nama_pakan']);
    $jenis_pakan = mysqli_real_escape_string($koneksi, $_POST['jenis_pakan']);
    $stok_kg     = (float) $_POST['stok_kg'];
    $satuan      = mysqli_real_escape_string($koneksi, $_POST['satuan']);

    $insert = mysqli_query($koneksi, "INSERT INTO pakan (nama_pakan, jenis_pakan, stok_kg, satuan) 
                                      VALUES ('$nama_pakan', '$jenis_pakan', '$stok_kg', '$satuan')");

    if ($insert) {
        echo "<script>alert('Data pakan berhasil ditambahkan!'); window.location='index.php';</script>";
    } else {
        $error = "Gagal menyimpan data: " . mysqli_error($koneksi);
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Tambah Data Pakan Baru</h5>
            </div>
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= $error; ?></div>
                <?php endif; ?>

                <form action="" method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Nama Pakan</label>
                            <input type="text" name="nama_pakan" class="form-control" placeholder="Contoh: Rumput Gajah / Pelet Konsentrat" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Jenis Pakan</label>
                            <select name="jenis_pakan" class="form-select" required>
                                <option value="">-- Pilih Jenis Pakan --</option>
                                <option value="Konsentrat">Konsentrat</option>
                                <option value="Hijauan">Hijauan</option>
                                <option value="Vitamin">Vitamin</option>
                                <option value="Suplemen">Suplemen</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Stok Awal</label>
                            <input type="number" step="0.01" name="stok_kg" class="form-control" placeholder="0.00" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Satuan</label>
                            <input type="text" name="satuan" class="form-control" value="Kg" required>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="index.php" class="btn btn-secondary"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
                        <button type="submit" name="simpan" class="btn btn-success"><i class="bi bi-save me-1"></i>Simpan Data Pakan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../../views/footer.php'; ?>