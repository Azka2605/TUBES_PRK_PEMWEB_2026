
<?php
// ==================================================
// 1. KONFIGURASI ERROR DAN SESSION
// ==================================================
error_reporting(E_ALL);
ini_set('display_errors', 1);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ==================================================
// 2. CEK LOGIN
// ==================================================
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['user'];

// ==================================================
// 3. KONEKSI DATABASE
// ==================================================
require_once __DIR__ . '/config/koneksi.php';

// Mendukung nama variabel koneksi $conn atau $koneksi
if (isset($conn) && $conn instanceof mysqli) {
    $db = $conn;
} elseif (isset($koneksi) && $koneksi instanceof mysqli) {
    $db = $koneksi;
} else {
    die("Koneksi database tidak ditemukan. Periksa config/koneksi.php");
}

// ==================================================
// 4. FUNGSI PEMBANTU DATABASE
// ==================================================
function tableExists($db, $table) {
    $table = $db->real_escape_string($table);
    $result = $db->query("SHOW TABLES LIKE '$table'");
    return $result && $result->num_rows > 0;
}

function getColumns($db, $table) {
    $columns = [];

    if (!tableExists($db, $table)) {
        return $columns;
    }

    $result = $db->query("SHOW COLUMNS FROM `$table`");

    while ($row = $result->fetch_assoc()) {
        $columns[] = $row['Field'];
    }

    return $columns;
}

function findColumn($columns, $candidates) {
    foreach ($candidates as $candidate) {
        if (in_array($candidate, $columns, true)) {
            return $candidate;
        }
    }

    return null;
}

function getCount($db, $table) {
    if (!tableExists($db, $table)) {
        return 0;
    }

    $result = $db->query("SELECT COUNT(*) AS total FROM `$table`");
    return (int) $result->fetch_assoc()['total'];
}

function rupiahNumber($number) {
    return number_format((float) $number, 1, ',', '.');
}

// ==================================================
// 5. MEMBACA STRUKTUR TABEL
// ==================================================
$ternakColumns = getColumns($db, 'ternak');
$kandangColumns = getColumns($db, 'kandang');
$pakanColumns = getColumns($db, 'pakan');
$riwayatColumns = getColumns($db, 'riwayat_bobot');

$statusColumn = findColumn(
    $ternakColumns,
    ['status', 'kondisi', 'status_ternak']
);

$bobotColumn = findColumn(
    $ternakColumns,
    ['bobot', 'bobot_terakhir', 'berat']
);

$kandangIdColumn = findColumn(
    $ternakColumns,
    ['id_kandang', 'kandang_id']
);

$stokColumn = findColumn(
    $pakanColumns,
    ['stok', 'stok_kg', 'jumlah_stok']
);

$riwayatBobotColumn = findColumn(
    $riwayatColumns,
    ['bobot', 'berat']
);

$tanggalColumn = findColumn(
    $riwayatColumns,
    ['tanggal', 'tanggal_catat', 'created_at']
);

// ==================================================
// 6. MENGHITUNG STATISTIK DASHBOARD
// ==================================================
$total_ternak = getCount($db, 'ternak');
$total_kandang = getCount($db, 'kandang');

$ternak_sakit = 0;
$ternak_karantina = 0;
$rata_rata_bobot = 0;
$jml_menipis = 0;

// Menghitung ternak sakit dan karantina
if ($statusColumn && tableExists($db, 'ternak')) {
    $sql = "
        SELECT
            SUM(LOWER(`$statusColumn`) = 'sakit') AS sakit,
            SUM(LOWER(`$statusColumn`) = 'karantina') AS karantina
        FROM ternak
    ";

    $result = $db->query($sql);
    $data = $result->fetch_assoc();

    $ternak_sakit = (int) ($data['sakit'] ?? 0);
    $ternak_karantina = (int) ($data['karantina'] ?? 0);
}

// Menghitung rata-rata bobot ternak
if ($bobotColumn && tableExists($db, 'ternak')) {
    $sql = "
        SELECT AVG(`$bobotColumn`) AS rata_rata
        FROM ternak
        WHERE `$bobotColumn` > 0
    ";

    $result = $db->query($sql);
    $data = $result->fetch_assoc();

    $rata_rata_bobot = (float) ($data['rata_rata'] ?? 0);
}

// Menghitung stok pakan di bawah 10 kg
if ($stokColumn && tableExists($db, 'pakan')) {
    $sql = "
        SELECT COUNT(*) AS total
        FROM pakan
        WHERE `$stokColumn` < 10
    ";

    $result = $db->query($sql);
    $jml_menipis = (int) $result->fetch_assoc()['total'];
}

// ==================================================
// 7. DATA RINGKASAN TERNAK PER KANDANG
// ==================================================
$ringkasan_ternak = [];

if (
    tableExists($db, 'ternak') &&
    tableExists($db, 'kandang') &&
    $kandangIdColumn
) {
    $kandangPrimary = findColumn(
        $kandangColumns,
        ['id_kandang', 'id']
    );

    $namaKandangColumn = findColumn(
        $kandangColumns,
        ['nama_kandang', 'nama', 'kode_kandang']
    );

    if ($kandangPrimary && $namaKandangColumn) {
        $sql = "
            SELECT
                k.`$namaKandangColumn` AS nama_kandang,
                COUNT(t.`$kandangIdColumn`) AS jumlah_ternak
            FROM kandang k
            LEFT JOIN ternak t
                ON k.`$kandangPrimary` = t.`$kandangIdColumn`
            GROUP BY k.`$kandangPrimary`
            ORDER BY k.`$kandangPrimary` ASC
        ";

        $result = $db->query($sql);

        while ($row = $result->fetch_assoc()) {
            $ringkasan_ternak[] = $row;
        }
    }
}

// ==================================================
// 8. DATA GRAFIK PERKEMBANGAN BOBOT
// ==================================================
$label_grafik = [];
$data_grafik = [];

if (
    tableExists($db, 'riwayat_bobot') &&
    $riwayatBobotColumn &&
    $tanggalColumn
) {
    $sql = "
        SELECT
            DATE(`$tanggalColumn`) AS tanggal,
            AVG(`$riwayatBobotColumn`) AS rata_bobot
        FROM riwayat_bobot
        GROUP BY DATE(`$tanggalColumn`)
        ORDER BY DATE(`$tanggalColumn`) ASC
        LIMIT 30
    ";

    $result = $db->query($sql);

    while ($row = $result->fetch_assoc()) {
        $label_grafik[] = date(
            'd/m/Y',
            strtotime($row['tanggal'])
        );

        $data_grafik[] = round(
            (float) $row['rata_bobot'],
            2
        );
    }
}

// ==================================================
// 9. INFORMASI USER
// ==================================================
$nama_user = $user['nama_lengkap']
    ?? $user['username']
    ?? 'Pengguna';

$role_user = $user['role'] ?? 'user';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard - Log Ternak K04</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
          rel="stylesheet">

    <style>
        body {
            background: #f5f7fb;
            font-family: Arial, sans-serif;
            color: #333;
        }

        .sidebar {
            min-height: 100vh;
            background: #183b2b;
            color: white;
            padding: 25px 15px;
        }

        .sidebar h5 {
            font-weight: bold;
            margin-bottom: 30px;
        }

        .sidebar a {
            display: block;
            color: #e9f5ed;
            text-decoration: none;
            padding: 12px 14px;
            border-radius: 8px;
            margin-bottom: 8px;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: #2d6548;
            color: white;
        }

        .main-content {
            padding: 30px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            gap: 15px;
            flex-wrap: wrap;
        }

        .stat-card {
            border: none;
            border-radius: 14px;
            padding: 22px;
            height: 100%;
            box-shadow: 0 3px 12px rgba(0,0,0,0.05);
        }

        .stat-card h6 {
            color: #777;
            font-size: 14px;
        }

        .stat-card h3 {
            font-size: 27px;
            font-weight: bold;
            margin-top: 12px;
        }

        .stat-icon {
            width: 45px;
            height: 45px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        .content-card {
            background: white;
            border: none;
            border-radius: 14px;
            padding: 22px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.05);
            height: 100%;
        }

        .content-card h5 {
            font-weight: bold;
            margin-bottom: 20px;
        }

        .table thead th {
            background: #f0f4f1;
            color: #355443;
            font-size: 13px;
        }

        .table td {
            vertical-align: middle;
        }

        .footer {
            color: #888;
            font-size: 13px;
            margin-top: 30px;
        }

        @media (max-width: 767px) {
            .sidebar {
                min-height: auto;
            }

            .main-content {
                padding: 18px;
            }
        }
    </style>
</head>

<body>

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
        <div class="col-md-2 sidebar">

            <h5>
                <i class="bi bi-hospital"></i>
                Log Ternak K04
            </h5>

            <a href="index.php" class="active">
                <i class="bi bi-grid"></i>
                Dashboard
            </a>

            <a href="ternak.php">
                <i class="bi bi-clipboard-data"></i>
                Data Ternak
            </a>

            <a href="kandang.php">
                <i class="bi bi-house"></i>
                Data Kandang
            </a>

            <a href="pakan.php">
                <i class="bi bi-basket"></i>
                Data Pakan
            </a>

            <a href="riwayat_bobot.php">
                <i class="bi bi-graph-up"></i>
                Riwayat Bobot
            </a>

            <hr>

            <a href="logout.php"
               onclick="return confirm('Apakah Anda yakin ingin keluar?')">
                <i class="bi bi-box-arrow-right"></i>
                Logout
            </a>

        </div>

        <!-- KONTEN UTAMA -->
        <div class="col-md-10 main-content">

            <!-- HEADER -->
            <div class="topbar">
                <div>
                    <h3 class="fw-bold mb-1">Dashboard</h3>
                    <p class="text-muted mb-0">
                        Selamat datang di Sistem Informasi Log Ternak K04.
                    </p>
                </div>

                <div class="text-end">
                    <strong><?= htmlspecialchars($nama_user) ?></strong>
                    <br>
                    <span class="badge bg-success">
                        <?= htmlspecialchars(ucfirst($role_user)) ?>
                    </span>
                </div>
            </div>

            <!-- KARTU STATISTIK -->
            <div class="row g-3 mb-4">

                <div class="col-md-3 col-sm-6">
                    <div class="card stat-card">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6>Total Ternak</h6>
                                <h3><?= $total_ternak ?></h3>
                            </div>
                            <div class="stat-icon bg-success-subtle text-success">
                                <i class="bi bi-clipboard-data"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="card stat-card">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6>Ternak Sakit</h6>
                                <h3><?= $ternak_sakit ?></h3>
                            </div>
                            <div class="stat-icon bg-danger-subtle text-danger">
                                <i class="bi bi-heart-pulse"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="card stat-card">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6>Jumlah Kandang</h6>
                                <h3><?= $total_kandang ?></h3>
                            </div>
                            <div class="stat-icon bg-primary-subtle text-primary">
                                <i class="bi bi-house"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="card stat-card">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6>Stok Pakan < 10 Kg</h6>
                                <h3><?= $jml_menipis ?></h3>
                            </div>
                            <div class="stat-icon bg-warning-subtle text-warning">
                                <i class="bi bi-basket"></i>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- INFORMASI TAMBAHAN -->
            <div class="row g-3 mb-4">

                <div class="col-md-6">
                    <div class="card stat-card">
                        <h6>Ternak Karantina</h6>
                        <h3><?= $ternak_karantina ?></h3>
                        <small class="text-muted">
                            Jumlah ternak dengan status karantina
                        </small>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card stat-card">
                        <h6>Rata-rata Bobot Terbaru</h6>
                        <h3>
                            <?= $rata_rata_bobot > 0
                                ? rupiahNumber($rata_rata_bobot) . ' Kg'
                                : '-' ?>
                        </h3>
                        <small class="text-muted">
                            Rata-rata bobot berdasarkan data ternak
                        </small>
                    </div>
                </div>

            </div>

            <!-- GRAFIK DAN TABEL -->
            <div class="row g-3">

                <!-- GRAFIK -->
                <div class="col-lg-7">
                    <div class="content-card">
                        <h5>
                            <i class="bi bi-graph-up text-success"></i>
                            Grafik Perkembangan Bobot Ternak
                        </h5>

                        <?php if (count($data_grafik) > 0): ?>
                            <canvas id="grafikBobot" height="120"></canvas>
                        <?php else: ?>
                            <p class="text-muted text-center py-4">
                                Belum ada data riwayat bobot ternak.
                            </p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- RINGKASAN KANDANG -->
                <div class="col-lg-5">
                    <div class="content-card">
                        <h5>
                            <i class="bi bi-house text-success"></i>
                            Ringkasan Ternak per Kandang
                        </h5>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Kandang</th>
                                        <th>Jumlah Ternak</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php if (count($ringkasan_ternak) > 0): ?>

                                        <?php foreach (
                                            $ringkasan_ternak as $index => $row
                                        ): ?>

                                            <tr>
                                                <td><?= $index + 1 ?></td>
                                                <td>
                                                    <?= htmlspecialchars(
                                                        $row['nama_kandang']
                                                    ) ?>
                                                </td>
                                                <td>
                                                    <?= (int) $row['jumlah_ternak'] ?>
                                                </td>
                                            </tr>

                                        <?php endforeach; ?>

                                    <?php else: ?>

                                        <tr>
                                            <td colspan="3"
                                                class="text-center text-muted">
                                                Data kandang belum tersedia.
                                            </td>
                                        </tr>

                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

            <!-- FOOTER -->
            <div class="footer text-center">
                &copy; <?= date('Y') ?> Log Ternak K04.
                Sistem Informasi Monitoring Peternakan.
            </div>

        </div>
    </div>
</div>

<!-- CHART JS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const labelBobot = <?= json_encode($label_grafik) ?>;
const dataBobot = <?= json_encode($data_grafik) ?>;

const canvas = document.getElementById('grafikBobot');

if (canvas && dataBobot.length > 0) {
    new Chart(canvas, {
        type: 'line',

        data: {
            labels: labelBobot,

            datasets: [{
                label: 'Rata-rata Bobot (Kg)',
                data: dataBobot,

                borderColor: '#198754',
                backgroundColor: 'rgba(25, 135, 84, 0.12)',

                borderWidth: 2,
                fill: true,
                tension: 0.3,

                pointRadius: 4,
                pointBackgroundColor: '#198754'
            }]
        },

        options: {
            responsive: true,

            plugins: {
                legend: {
                    display: true
                }
            },

            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Bobot (Kg)'
                    }
                },

                x: {
                    title: {
                        display: true,
                        text: 'Tanggal'
                    }
                }
            }
        }
    });
}
</script>

</body>
</html>
