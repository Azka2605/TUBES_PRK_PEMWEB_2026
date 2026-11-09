<?php
include 'config/koneksi.php';
include 'views/header.php';

// Statistik dashboard
$total_ternak  = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS jml FROM ternak"))['jml'];
$ternak_sakit  = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS jml FROM ternak WHERE status_kesehatan = 'Sakit'"))['jml'];
$ringkasan_ternak = mysqli_fetch_assoc(mysqli_query(
    $koneksi,
    "SELECT
        (SELECT COUNT(*) FROM ternak WHERE status_kesehatan = 'Karantina') AS ternak_karantina,
        (SELECT AVG(rb.bobot_kg)
         FROM riwayat_bobot rb
         WHERE NOT EXISTS (
             SELECT 1
             FROM riwayat_bobot rb_terbaru
             WHERE rb_terbaru.id_ternak = rb.id_ternak
               AND (
                   rb_terbaru.tgl_timbang > rb.tgl_timbang
                   OR (rb_terbaru.tgl_timbang = rb.tgl_timbang AND rb_terbaru.id_bobot > rb.id_bobot)
               )
         )) AS rata_bobot_terbaru"
));
$total_kandang = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS jml FROM kandang"))['jml'];
$stok_menipis  = mysqli_query($koneksi, "SELECT nama_pakan, stok_kg FROM pakan WHERE stok_kg < 10 ORDER BY stok_kg ASC");
$jml_menipis   = mysqli_num_rows($stok_menipis);
$distribusi_status = ['Sehat' => 0, 'Sakit' => 0, 'Karantina' => 0];
$hasil_status = mysqli_query($koneksi, "SELECT status_kesehatan, COUNT(*) AS jumlah FROM ternak GROUP BY status_kesehatan");
while ($status = mysqli_fetch_assoc($hasil_status)) {
    $distribusi_status[$status['status_kesehatan']] = (int) $status['jumlah'];
}
$ternak_per_kandang = mysqli_query(
    $koneksi,
    "SELECT k.nama_kandang, k.kapasitas, COUNT(t.id_ternak) AS jumlah_ternak
     FROM kandang k
     LEFT JOIN ternak t ON t.id_kandang = k.id_kandang
     GROUP BY k.id_kandang, k.nama_kandang, k.kapasitas
     ORDER BY k.nama_kandang"
);
$pemeriksaan_terbaru = mysqli_query(
    $koneksi,
    "SELECT lk.tgl_periksa, lk.diagnosa, t.tag_number, u.nama_lengkap AS dokter
     FROM log_kesehatan lk
     JOIN ternak t ON t.id_ternak = lk.id_ternak
     LEFT JOIN users u ON u.id_user = lk.id_dokter
     ORDER BY lk.tgl_periksa DESC, lk.id_kesehatan DESC
     LIMIT 5"
);
$penggunaan_harian = mysqli_query(
    $koneksi,
    "SELECT DATE(tgl_pemberian) AS tanggal, SUM(jumlah_kg) AS total_kg
     FROM log_pemberian_pakan
     WHERE tgl_pemberian >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
       AND tgl_pemberian < DATE_ADD(CURDATE(), INTERVAL 1 DAY)
     GROUP BY DATE(tgl_pemberian)
     ORDER BY tanggal"
);
$total_pakan_harian = [];
while ($pemakaian = mysqli_fetch_assoc($penggunaan_harian)) {
    $total_pakan_harian[$pemakaian['tanggal']] = (float) $pemakaian['total_kg'];
}
$label_pakan_harian = [];
$data_pakan_harian = [];
$tanggal = new DateTimeImmutable('today');
for ($hari = 6; $hari >= 0; $hari--) {
    $tanggal_harian = $tanggal->modify("-$hari days");
    $tanggal_key = $tanggal_harian->format('Y-m-d');
    $label_pakan_harian[] = $tanggal_harian->format('d/m');
    $data_pakan_harian[] = $total_pakan_harian[$tanggal_key] ?? 0;
}
?>

<style>
    .dashboard-page {
        --dashboard-green-dark: #146c43;
        --dashboard-green-soft: #eaf5ee;
    }

    .dashboard-page .dashboard-title {
        color: #234b36;
    }

    .dashboard-page .stat-card,
    .dashboard-page .dashboard-panel {
        border: 1px solid #e4ece6;
        border-radius: 0.85rem;
        box-shadow: 0 0.25rem 0.85rem rgba(35, 75, 54, 0.07);
    }

    .dashboard-page .stat-card {
        border-left: 4px solid var(--stat-color);
        background: #fff;
        color: #234b36;
    }

    .dashboard-page .stat-card .card-title {
        color: #6c7d72;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .dashboard-page .stat-card h2 {
        color: var(--stat-color);
        font-weight: 700;
    }

    .dashboard-page .stat-icon {
        display: grid;
        width: 3rem;
        height: 3rem;
        place-items: center;
        border-radius: 0.75rem;
        background: var(--stat-soft);
        color: var(--stat-color);
        font-size: 1.35rem;
    }

    .dashboard-page .stat-primary { --stat-color: #27865a; --stat-soft: #eaf5ee; }
    .dashboard-page .stat-danger { --stat-color: #bd5b5b; --stat-soft: #f9eded; }
    .dashboard-page .stat-warning { --stat-color: #a77a22; --stat-soft: #fbf4e3; }
    .dashboard-page .stat-info { --stat-color: #4a8173; --stat-soft: #eaf3f1; }

    .dashboard-page .panel-heading {
        border-bottom: 1px solid #dcebe1;
        border-radius: 0.85rem 0.85rem 0 0;
        background: var(--dashboard-green-soft);
        color: var(--dashboard-green-dark);
    }

    .dashboard-page .table thead th {
        background: #f3f8f4;
        color: #345b44;
        font-weight: 600;
        white-space: nowrap;
    }

    .dashboard-page .table > :not(caption) > * > * {
        padding: 0.8rem 1rem;
    }

    .dashboard-page .alert-warning {
        border-color: #f0dfb5;
        background: #fff9eb;
        color: #70551d;
    }
</style>

<div class="container-fluid my-3 dashboard-page">
    <div class="mb-4">
        <h2 class="dashboard-title mb-1">Dashboard</h2>
        <p class="text-muted mb-0">Ringkasan kondisi ternak, kandang, dan pakan.</p>
    </div>

    <div class="row g-3">
        <div class="col-md-3">
            <div class="card stat-card stat-primary h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Total Ternak</h6>
                        <h2 class="mb-0"><?= $total_ternak ?></h2>
                    </div>
                    <span class="stat-icon"><i class="bi bi-box-seam" aria-hidden="true"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card stat-danger h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Ternak Sakit</h6>
                        <h2 class="mb-0"><?= $ternak_sakit ?></h2>
                    </div>
                    <span class="stat-icon"><i class="bi bi-heart-pulse" aria-hidden="true"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card stat-primary h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Jumlah Kandang</h6>
                        <h2 class="mb-0"><?= $total_kandang ?></h2>
                    </div>
                    <span class="stat-icon"><i class="bi bi-house-door" aria-hidden="true"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card stat-warning h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Stok Pakan &lt; 10 Kg</h6>
                        <h2 class="mb-0"><?= $jml_menipis ?> jenis</h2>
                    </div>
                    <span class="stat-icon"><i class="bi bi-basket" aria-hidden="true"></i></span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mt-1">
        <div class="col-md-6">
            <div class="card stat-card stat-warning h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Ternak Karantina</h6>
                        <h2 class="mb-0"><?= (int) $ringkasan_ternak['ternak_karantina'] ?></h2>
                    </div>
                    <span class="stat-icon"><i class="bi bi-shield-plus" aria-hidden="true"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card stat-card stat-info h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Rata-rata Bobot Terbaru</h6>
                        <h2 class="mb-0">
                            <?= $ringkasan_ternak['rata_bobot_terbaru'] !== null
                                ? number_format((float) $ringkasan_ternak['rata_bobot_terbaru'], 2, ',', '.') . ' kg'
                                : '-' ?>
                        </h2>
                    </div>
                    <span class="stat-icon"><i class="bi bi-speedometer2" aria-hidden="true"></i></span>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6 col-lg-5">
            <div class="card dashboard-panel h-100">
                <div class="card-header panel-heading">
                    <h5 class="mb-0">Distribusi Status Ternak</h5>
                </div>
                <div class="card-body">
                    <canvas id="chartStatusTernak" aria-label="Chart distribusi status ternak" role="img"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-7 mt-4 mt-md-0">
            <div class="card dashboard-panel h-100">
                <div class="card-header panel-heading">
                    <h5 class="mb-0">Penggunaan Pakan 7 Hari Terakhir</h5>
                </div>
                <div class="card-body">
                    <canvas id="chartPakanHarian" aria-label="Chart penggunaan pakan harian dalam kilogram" role="img"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="card dashboard-panel mt-4">
        <div class="card-header panel-heading">
            <h5 class="mb-0">Ternak per Kandang</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Nama Kandang</th>
                        <th>Jumlah Ternak</th>
                        <th>Kapasitas</th>
                        <th>Keterisian</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($ternak_per_kandang) > 0): ?>
                        <?php while ($kandang = mysqli_fetch_assoc($ternak_per_kandang)):
                            $persentase = (int) $kandang['kapasitas'] > 0
                                ? ((int) $kandang['jumlah_ternak'] / (int) $kandang['kapasitas']) * 100
                                : 0;
                            $lebar_bar = min($persentase, 100);
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($kandang['nama_kandang']) ?></td>
                                <td><?= (int) $kandang['jumlah_ternak'] ?></td>
                                <td><?= (int) $kandang['kapasitas'] ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" role="progressbar"
                                             aria-label="Keterisian kandang"
                                             aria-valuenow="<?= (int) $lebar_bar ?>"
                                             aria-valuemin="0" aria-valuemax="100">
                                            <div class="progress-bar <?= $persentase > 100 ? 'bg-danger' : 'bg-success' ?>"
                                                 style="width: <?= $lebar_bar ?>%"></div>
                                        </div>
                                        <span><?= number_format($persentase, 1) ?>%</span>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted">Belum ada data kandang.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card dashboard-panel mt-4">
        <div class="card-header panel-heading d-flex justify-content-between align-items-center">
            <h5 class="mb-0">5 Pemeriksaan Kesehatan Terbaru</h5>
            <a href="modul/laporan/index.php" class="btn btn-outline-success btn-sm">Lihat Laporan</a>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>No. Tag</th>
                        <th>Diagnosa</th>
                        <th>Dokter</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($pemeriksaan_terbaru) > 0): ?>
                        <?php while ($pemeriksaan = mysqli_fetch_assoc($pemeriksaan_terbaru)): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($pemeriksaan['tgl_periksa'])) ?></td>
                                <td><?= htmlspecialchars($pemeriksaan['tag_number']) ?></td>
                                <td><?= htmlspecialchars($pemeriksaan['diagnosa']) ?></td>
                                <td><?= htmlspecialchars($pemeriksaan['dokter'] ?? '-') ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted">Belum ada pemeriksaan kesehatan.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if ($jml_menipis > 0): ?>
    <div class="alert alert-warning mt-4 shadow-sm">
        <strong>Peringatan stok pakan menipis:</strong>
        <ul class="mb-0">
            <?php while ($p = mysqli_fetch_assoc($stok_menipis)): ?>
                <li><?= htmlspecialchars($p['nama_pakan']) ?> (sisa <?= $p['stok_kg'] ?> kg)</li>
            <?php endwhile; ?>
        </ul>
    </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const dataStatusTernak = <?= json_encode(array_values($distribusi_status), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;

    new Chart(document.getElementById('chartStatusTernak'), {
        type: 'doughnut',
        data: {
            labels: ['Sehat', 'Sakit', 'Karantina'],
            datasets: [{
                data: dataStatusTernak,
                backgroundColor: ['#27865a', '#bd5b5b', '#d2a23b'],
                borderColor: '#fff',
                borderWidth: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    new Chart(document.getElementById('chartPakanHarian'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($label_pakan_harian, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>,
            datasets: [{
                label: 'Pakan (kg)',
                data: <?= json_encode($data_pakan_harian, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>,
                backgroundColor: 'rgba(39, 134, 90, 0.78)',
                borderColor: '#27865a',
                borderWidth: 1,
                borderRadius: 6,
                maxBarThickness: 42
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    title: { display: true, text: 'Kilogram' }
                }
            }
        }
    });
</script>

<?php include 'views/footer.php'; ?>