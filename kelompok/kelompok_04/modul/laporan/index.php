<?php
include '../../config/koneksi.php';
include '../../views/header.php';

// --- Filter rentang tanggal (default: bulan ini) ---
$dari   = $_GET['dari']   ?? date('Y-m-01');
$sampai = $_GET['sampai'] ?? date('Y-m-t');
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dari))   $dari   = date('Y-m-01');
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $sampai)) $sampai = date('Y-m-t');

// --- Rekap rekam medis ---
$sql_medis = "SELECT lk.tgl_periksa, t.tag_number, t.jenis_ras, lk.diagnosa,
                     lk.tindakan_medis, lk.obat_vaksin, u.nama_lengkap AS dokter
              FROM log_kesehatan lk
              JOIN ternak t ON t.id_ternak = lk.id_ternak
              LEFT JOIN users u ON u.id_user = lk.id_dokter
              WHERE lk.tgl_periksa BETWEEN ? AND ?
              ORDER BY lk.tgl_periksa DESC";
$stmt = mysqli_prepare($koneksi, $sql_medis);
mysqli_stmt_bind_param($stmt, 'ss', $dari, $sampai);
mysqli_stmt_execute($stmt);
$medis = mysqli_stmt_get_result($stmt);

// --- Rekap penggunaan pakan ---
$sql_pakan = "SELECT DATE(lp.tgl_pemberian) AS tgl, p.nama_pakan, k.nama_kandang,
                     lp.jumlah_kg, u.nama_lengkap AS petugas
              FROM log_pemberian_pakan lp
              JOIN pakan p ON p.id_pakan = lp.id_pakan
              JOIN kandang k ON k.id_kandang = lp.id_kandang
              LEFT JOIN users u ON u.id_user = lp.id_peternak
              WHERE DATE(lp.tgl_pemberian) BETWEEN ? AND ?
              ORDER BY lp.tgl_pemberian DESC";
$stmt = mysqli_prepare($koneksi, $sql_pakan);
mysqli_stmt_bind_param($stmt, 'ss', $dari, $sampai);
mysqli_stmt_execute($stmt);
$pakan = mysqli_stmt_get_result($stmt);
$total_pakan = 0;
?>

<style>
@page { size: A4 landscape; margin: 1cm; }
@media print {
    nav.navbar, footer, .no-print { display: none !important; }
    body { background: #fff !important; font-size: 12px; }
    .container { max-width: 100% !important; width: 100% !important; margin: 0 !important; padding: 0 !important; }
    .table-responsive { overflow: visible !important; }
    table { width: 100% !important; font-size: 11px; }
    th, td { word-break: break-word; }
}
</style>

<div>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Rekapitulasi Laporan</h2>
        <button onclick="window.print()" class="btn btn-success no-print">
            <i class="bi bi-printer me-1"></i> Cetak Laporan
        </button>
    </div>

    <form method="get" class="row g-2 align-items-end mb-4 no-print">
        <div class="col-auto">
            <label class="form-label mb-0">Dari</label>
            <input type="date" name="dari" value="<?= htmlspecialchars($dari) ?>" class="form-control">
        </div>
        <div class="col-auto">
            <label class="form-label mb-0">Sampai</label>
            <input type="date" name="sampai" value="<?= htmlspecialchars($sampai) ?>" class="form-control">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary">Tampilkan</button>
        </div>
    </form>

    <p class="text-muted">Periode: <?= date('d/m/Y', strtotime($dari)) ?> s/d <?= date('d/m/Y', strtotime($sampai)) ?></p>

    <h4 class="mt-4">Rekam Medis</h4>
    <div class="table-responsive">
        <table class="table table-bordered table-striped bg-white">
            <thead class="table-success">
                <tr>
                    <th>Tanggal</th><th>Tag</th><th>Ras</th><th>Diagnosa</th>
                    <th>Tindakan</th><th>Obat/Vaksin</th><th>Dokter</th>
                </tr>
            </thead>
            <tbody>
            <?php if (mysqli_num_rows($medis) == 0): ?>
                <tr><td colspan="7" class="text-center text-muted">Tidak ada data pada periode ini.</td></tr>
            <?php endif; ?>
            <?php while ($m = mysqli_fetch_assoc($medis)): ?>
                <tr>
                    <td><?= date('d/m/Y', strtotime($m['tgl_periksa'])) ?></td>
                    <td><?= htmlspecialchars($m['tag_number']) ?></td>
                    <td><?= htmlspecialchars($m['jenis_ras']) ?></td>
                    <td><?= htmlspecialchars($m['diagnosa']) ?></td>
                    <td><?= htmlspecialchars($m['tindakan_medis'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($m['obat_vaksin'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($m['dokter'] ?? '-') ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <h4 class="mt-4">Penggunaan Pakan</h4>
    <div class="table-responsive">
        <table class="table table-bordered table-striped bg-white">
            <thead class="table-success">
                <tr>
                    <th>Tanggal</th><th>Pakan</th><th>Kandang</th>
                    <th class="text-end">Jumlah (kg)</th><th>Petugas</th>
                </tr>
            </thead>
            <tbody>
            <?php if (mysqli_num_rows($pakan) == 0): ?>
                <tr><td colspan="5" class="text-center text-muted">Tidak ada data pada periode ini.</td></tr>
            <?php endif; ?>
            <?php while ($p = mysqli_fetch_assoc($pakan)): $total_pakan += $p['jumlah_kg']; ?>
                <tr>
                    <td><?= date('d/m/Y', strtotime($p['tgl'])) ?></td>
                    <td><?= htmlspecialchars($p['nama_pakan']) ?></td>
                    <td><?= htmlspecialchars($p['nama_kandang']) ?></td>
                    <td class="text-end"><?= number_format($p['jumlah_kg'], 2, ',', '.') ?></td>
                    <td><?= htmlspecialchars($p['petugas'] ?? '-') ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
            <tfoot>
                <tr class="fw-bold">
                    <td colspan="3" class="text-end">Total</td>
                    <td class="text-end"><?= number_format($total_pakan, 2, ',', '.') ?></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<?php include '../../views/footer.php'; ?>