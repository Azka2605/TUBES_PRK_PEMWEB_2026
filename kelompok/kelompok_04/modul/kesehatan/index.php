<?php
include '../../config/koneksi.php';
include '../../views/header.php';

$filter_jenis = isset($_GET['jenis_ternak']) && is_string($_GET['jenis_ternak'])
    ? $_GET['jenis_ternak']
    : 'semua';
$jenis_ternak_valid = [
    'sapi' => 'Sapi%',
    'kambing' => 'Kambing%',
    'ayam' => 'Ayam Petelur%'
];
if ($filter_jenis !== 'semua' && !isset($jenis_ternak_valid[$filter_jenis])) {
    $filter_jenis = 'semua';
}
$label_jenis_ternak = [
    'semua' => 'Semua Ternak',
    'sapi' => 'Sapi',
    'kambing' => 'Kambing',
    'ayam' => 'Ayam Petelur'
];

// Daftar rekam medis + JOIN ke ternak (tag number) dan users (nama dokter)
$sql = "SELECT lk.id_kesehatan, lk.tgl_periksa, lk.diagnosa, lk.tindakan_medis, lk.obat_vaksin,
               t.tag_number, t.jenis_ras, t.status_kesehatan,
               u.nama_lengkap AS nama_dokter
        FROM log_kesehatan lk
        JOIN ternak t ON lk.id_ternak = t.id_ternak
        LEFT JOIN users u ON lk.id_dokter = u.id_user";
if ($filter_jenis !== 'semua') {
    $sql .= " WHERE t.jenis_ras LIKE ?";
}
$sql .= " ORDER BY lk.tgl_periksa DESC, lk.id_kesehatan DESC";
$stmt = mysqli_prepare($koneksi, $sql);
if ($filter_jenis !== 'semua') {
    $jenis_ras_filter = $jenis_ternak_valid[$filter_jenis];
    mysqli_stmt_bind_param($stmt, 's', $jenis_ras_filter);
}
mysqli_stmt_execute($stmt);
$query = mysqli_stmt_get_result($stmt);

if (!$query) {
    die('Query gagal: ' . mysqli_error($koneksi));
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-heart-pulse me-2"></i>Rekam Medis Ternak</h2>
    <a href="tambah.php" class="btn btn-success"><i class="bi bi-plus-circle me-1"></i>Tambah Pemeriksaan</a>
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

<style>
    .health-filter .nav-link {
        color: var(--brand-dark);
    }

    .health-filter .nav-link.active {
        background-color: var(--brand);
        color: #fff;
    }
</style>

<div class="card shadow-sm">
    <div class="card-body border-bottom">
        <div class="nav nav-pills flex-wrap gap-2 health-filter" aria-label="Filter rekam medis berdasarkan jenis ternak">
            <?php foreach ($label_jenis_ternak as $jenis => $label): ?>
                <a href="index.php?jenis_ternak=<?= urlencode($jenis) ?>"
                   class="nav-link <?= $filter_jenis === $jenis ? 'active' : '' ?>"
                   <?= $filter_jenis === $jenis ? 'aria-current="page"' : '' ?>>
                    <?php if ($jenis === 'ayam'): ?>
                        <i class="bi bi-egg me-1" aria-hidden="true"></i>
                    <?php elseif ($jenis === 'semua'): ?>
                        <i class="bi bi-grid me-1" aria-hidden="true"></i>
                    <?php else: ?>
                        <i class="bi bi-heart-pulse me-1" aria-hidden="true"></i>
                    <?php endif; ?>
                    <?= htmlspecialchars($label) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle mb-0">
                <thead class="table-success">
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>No Tag</th>
                        <th>Jenis / Ras</th>
                        <th>Dokter</th>
                        <th>Diagnosa</th>
                        <th>Tindakan Medis</th>
                        <th>Obat / Vaksin</th>
                        <th>Status Kesehatan</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    if (mysqli_num_rows($query) > 0):
                        while ($row = mysqli_fetch_assoc($query)):
                            $badge_status = 'bg-success';
                            if ($row['status_kesehatan'] == 'Sakit') $badge_status = 'bg-danger';
                            if ($row['status_kesehatan'] == 'Karantina') $badge_status = 'bg-warning text-dark';
                    ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= htmlspecialchars($row['tgl_periksa']); ?></td>
                        <td><strong><?= htmlspecialchars($row['tag_number']); ?></strong></td>
                        <td><?= htmlspecialchars($row['jenis_ras']); ?></td>
                        <td><?= htmlspecialchars($row['nama_dokter'] ?? 'Tidak diketahui'); ?></td>
                        <td><?= htmlspecialchars($row['diagnosa']); ?></td>
                        <td><?= htmlspecialchars($row['tindakan_medis'] ?? '-'); ?></td>
                        <td><?= htmlspecialchars($row['obat_vaksin'] ?? '-'); ?></td>
                        <td><span class="badge <?= $badge_status; ?>"><?= htmlspecialchars($row['status_kesehatan']); ?></span></td>
                        <td class="text-center">
                            <a href="edit.php?id=<?= (int)$row['id_kesehatan']; ?>" class="btn btn-warning btn-sm me-1"><i class="bi bi-pencil-square"></i> Edit</a>
                            <a href="hapus.php?id=<?= (int)$row['id_kesehatan']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus rekam medis ini?')"><i class="bi bi-trash"></i> Hapus</a>
                        </td>
                    </tr>
                    <?php
                        endwhile;
                    else:
                    ?>
                    <tr>
                        <td colspan="10" class="text-center py-4 text-muted">Belum ada rekam medis untuk <?= htmlspecialchars($label_jenis_ternak[$filter_jenis]) ?>. Klik tombol tambah di atas.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../../views/footer.php'; ?>