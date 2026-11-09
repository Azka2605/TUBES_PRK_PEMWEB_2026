<?php
include '../../config/koneksi.php';
include '../../views/header.php';

// Filter berdasarkan ternak
$filter_ternak = isset($_GET['id_ternak']) ? (int)$_GET['id_ternak'] : 0;

$sql = "SELECT rb.id_bobot, rb.tgl_timbang, rb.bobot_kg, rb.catatan,
               t.tag_number, t.jenis_ras
        FROM riwayat_bobot rb
        JOIN ternak t ON rb.id_ternak = t.id_ternak";
if ($filter_ternak > 0) {
    $sql .= " WHERE rb.id_ternak = ?";
}
$sql .= " ORDER BY rb.tgl_timbang DESC, rb.id_bobot DESC";

$stmt = mysqli_prepare($koneksi, $sql);
if ($filter_ternak > 0) {
    mysqli_stmt_bind_param($stmt, "i", $filter_ternak);
}
mysqli_stmt_execute($stmt);
$query = mysqli_stmt_get_result($stmt);

// Ambil list ternak untuk dropdown filter
$ternak_list = mysqli_query($koneksi, "SELECT id_ternak, tag_number FROM ternak ORDER BY tag_number ASC");
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-speedometer me-2"></i>Riwayat Bobot Ternak</h2>
    <a href="tambah.php" class="btn btn-success"><i class="bi bi-plus-circle me-1"></i>Tambah Penimbangan</a>
</div>

<?php if (isset($_GET['status'])): ?>
    <?php if ($_GET['status'] === 'sukses'): ?>
        <div class="alert alert-success">Data berhasil disimpan.</div>
    <?php elseif ($_GET['status'] === 'hapus'): ?>
        <div class="alert alert-success">Data berhasil dihapus.</div>
    <?php elseif ($_GET['status'] === 'gagal'): ?>
        <div class="alert alert-danger">Terjadi kesalahan, data tidak tersimpan.</div>
    <?php endif; ?>
<?php endif; ?>

<!-- Filter Ternak -->
<div class="card mb-4 shadow-sm">
    <div class="card-body">
        <form method="GET" action="" class="row g-3 align-items-center">
            <div class="col-auto">
                <label class="col-form-label fw-bold">Filter Ternak:</label>
            </div>
            <div class="col-auto">
                <select name="id_ternak" class="form-select" onchange="this.form.submit()">
                    <option value="0">-- Semua Ternak --</option>
                    <?php while ($t = mysqli_fetch_assoc($ternak_list)): ?>
                        <option value="<?= (int)$t['id_ternak']; ?>" <?= $filter_ternak == $t['id_ternak'] ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($t['tag_number']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <?php if ($filter_ternak > 0): ?>
                <div class="col-auto">
                    <a href="index.php" class="btn btn-outline-secondary btn-sm">Reset Filter</a>
                </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle mb-0">
                <thead class="table-success">
                    <tr>
                        <th>No</th>
                        <th>Tanggal Timbang</th>
                        <th>No Tag</th>
                        <th>Jenis / Ras</th>
                        <th>Bobot (kg)</th>
                        <th>Catatan</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    if (mysqli_num_rows($query) > 0):
                        while ($row = mysqli_fetch_assoc($query)):
                    ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= htmlspecialchars($row['tgl_timbang']); ?></td>
                        <td><strong><?= htmlspecialchars($row['tag_number']); ?></strong></td>
                        <td><?= htmlspecialchars($row['jenis_ras']); ?></td>
                        <td><?= number_format((float)$row['bobot_kg'], 2, ',', '.'); ?></td>
                        <td><?= htmlspecialchars($row['catatan'] ?? '-'); ?></td>
                        <td class="text-center">
                            <a href="edit.php?id=<?= (int)$row['id_bobot']; ?>" class="btn btn-warning btn-sm me-1"><i class="bi bi-pencil-square"></i> Edit</a>
                            <a href="hapus.php?id=<?= (int)$row['id_bobot']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus data penimbangan ini?')"><i class="bi bi-trash"></i> Hapus</a>
                        </td>
                    </tr>
                    <?php
                        endwhile;
                    else:
                    ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">Belum ada data penimbangan. Klik tombol tambah di atas.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../../views/footer.php'; ?>
