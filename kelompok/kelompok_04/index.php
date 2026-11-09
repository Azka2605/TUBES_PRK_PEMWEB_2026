<?php
include 'config/koneksi.php';
include 'views/header.php';

// Statistik dashboard
$total_ternak  = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS jml FROM ternak"))['jml'];
$ternak_sakit  = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS jml FROM ternak WHERE status = 'Sakit'"))['jml'];
$total_kandang = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS jml FROM kandang"))['jml'];
$stok_menipis  = mysqli_query($koneksi, "SELECT nama_pakan, stok_kg FROM pakan WHERE stok_kg < 10 ORDER BY stok_kg ASC");
$jml_menipis   = mysqli_num_rows($stok_menipis);
?>

<div class="container-fluid my-3">
    <h2 class="mb-4">Dashboard</h2>

    <div class="row g-3">
        <div class="col-md-3">
            <div class="card text-bg-primary h-100">
                <div class="card-body">
                    <h6 class="card-title">Total Ternak</h6>
                    <h2><?= $total_ternak ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-danger h-100">
                <div class="card-body">
                    <h6 class="card-title">Ternak Sakit</h6>
                    <h2><?= $ternak_sakit ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-success h-100">
                <div class="card-body">
                    <h6 class="card-title">Jumlah Kandang</h6>
                    <h2><?= $total_kandang ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-warning h-100">
                <div class="card-body">
                    <h6 class="card-title">Stok Pakan &lt; 10 Kg</h6>
                    <h2><?= $jml_menipis ?> jenis</h2>
                </div>
            </div>
        </div>
    </div>

    <?php if ($jml_menipis > 0): ?>
    <div class="alert alert-warning mt-4">
        <strong>Peringatan stok pakan menipis:</strong>
        <ul class="mb-0">
            <?php while ($p = mysqli_fetch_assoc($stok_menipis)): ?>
                <li><?= htmlspecialchars($p['nama_pakan']) ?> (sisa <?= $p['stok_kg'] ?> kg)</li>
            <?php endwhile; ?>
        </ul>
    </div>
    <?php endif; ?>
</div>

<?php include 'views/footer.php'; ?>