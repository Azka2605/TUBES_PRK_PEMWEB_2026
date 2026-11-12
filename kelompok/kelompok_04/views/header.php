
<?php
// ==========================================
// 1. MEMULAI SESSION
// ==========================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ==========================================
// 2. MENGAMBIL DATA PENGGUNA
// ==========================================
$user = $_SESSION['user'] ?? [];

$nama_user = $user['nama_lengkap']
    ?? $user['username']
    ?? 'Pengguna';

$role_user = strtolower(trim($user['role'] ?? ''));

// ==========================================
// 3. MENENTUKAN HAK AKSES ROLE
// ==========================================
$is_admin = ($role_user === 'admin');
$is_peternak = ($role_user === 'peternak');
$is_dokter = ($role_user === 'dokter');

// ==========================================
// 4. MENGAMBIL BASE URL
// ==========================================
if (!isset($base_url)) {
    $base_url = '/TUBES_PRK_PEMWEB_2026/kelompok/kelompok_04';
}

// ==========================================
// 5. MENGAMBIL ALAMAT HALAMAN SAAT INI
// ==========================================
$current_uri = $_SERVER['REQUEST_URI'] ?? '';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Sistem Log Ternak - Kelompok 04</title>

    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>

<body class="bg-light d-flex flex-column min-vh-100">

<!-- ==========================================
     NAVBAR UTAMA
========================================== -->

<nav class="navbar navbar-expand-lg navbar-dark bg-success shadow-sm">

    <div class="container">

        <!-- BRAND -->
        <a class="navbar-brand fw-bold"
           href="<?= htmlspecialchars($base_url); ?>/index.php">

            <i class="bi bi-box-seam me-2"></i>
            Log Ternak K04

        </a>

        <!-- TOGGLE MENU MOBILE -->
        <button class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarNav">

            <span class="navbar-toggler-icon"></span>

        </button>

        <div class="collapse navbar-collapse" id="navbarNav">

            <ul class="navbar-nav ms-auto">

                <!-- ==================================
                     DASHBOARD: SEMUA ROLE
                =================================== -->

                <li class="nav-item">

                    <a class="nav-link <?= (strpos($current_uri, '/modul/') === false) ? 'active' : ''; ?>"
                       href="<?= htmlspecialchars($base_url); ?>/index.php">

                        <i class="bi bi-speedometer2 me-1"></i>
                        Dashboard

                    </a>

                </li>


                <!-- ==================================
                     USER: KHUSUS ADMIN
                =================================== -->

                <?php if ($is_admin): ?>

                <li class="nav-item">

                    <a class="nav-link <?= (strpos($current_uri, '/modul/user/') !== false) ? 'active' : ''; ?>"
                       href="<?= htmlspecialchars($base_url); ?>/modul/user/index.php">

                        <i class="bi bi-people me-1"></i>
                        User

                    </a>

                </li>

                <?php endif; ?>


                <!-- ==================================
                     KANDANG: ADMIN DAN PETERNAK
                =================================== -->

                <?php if ($is_admin || $is_peternak): ?>

                <li class="nav-item">

                    <a class="nav-link <?= (strpos($current_uri, '/modul/kandang/') !== false) ? 'active' : ''; ?>"
                       href="<?= htmlspecialchars($base_url); ?>/modul/kandang/index.php">

                        <i class="bi bi-house-door me-1"></i>
                        Kandang

                    </a>

                </li>

                <?php endif; ?>


                <!-- ==================================
                     TERNAK: SEMUA ROLE
                =================================== -->

                <li class="nav-item">

                    <a class="nav-link <?= (strpos($current_uri, '/modul/ternak/') !== false) ? 'active' : ''; ?>"
                       href="<?= htmlspecialchars($base_url); ?>/modul/ternak/index.php">

                        <i class="bi bi-box-seam me-1"></i>
                        Ternak

                    </a>

                </li>


                <!-- ==================================
                     KESEHATAN
                     REKAM MEDIS: ADMIN DAN DOKTER
                     BOBOT: SEMUA ROLE
                =================================== -->

                <?php if ($is_admin || $is_peternak || $is_dokter): ?>

                <li class="nav-item dropdown">

                    <a class="nav-link dropdown-toggle <?= (
                        strpos($current_uri, '/modul/kesehatan/') !== false ||
                        strpos($current_uri, '/modul/bobot/') !== false
                    ) ? 'active' : ''; ?>"
                       href="#"
                       role="button"
                       data-bs-toggle="dropdown"
                       aria-expanded="false">

                        <i class="bi bi-heart-pulse me-1"></i>
                        Kesehatan

                    </a>

                    <ul class="dropdown-menu dropdown-menu-end">

                        <!-- REKAM MEDIS: ADMIN DAN DOKTER -->
                        <?php if ($is_admin || $is_dokter): ?>

                        <li>

                            <a class="dropdown-item <?= (strpos($current_uri, '/modul/kesehatan/') !== false) ? 'active' : ''; ?>"
                               href="<?= htmlspecialchars($base_url); ?>/modul/kesehatan/index.php">

                                <i class="bi bi-clipboard2-pulse me-2"></i>
                                Rekam Medis

                            </a>

                        </li>

                        <?php endif; ?>


                        <!-- RIWAYAT BOBOT: SEMUA ROLE -->

                        <li>

                            <a class="dropdown-item <?= (strpos($current_uri, '/modul/bobot/') !== false) ? 'active' : ''; ?>"
                               href="<?= htmlspecialchars($base_url); ?>/modul/bobot/index.php">

                                <i class="bi bi-speedometer me-2"></i>
                                Bobot

                            </a>

                        </li>

                    </ul>

                </li>

                <?php endif; ?>


                <!-- ==================================
                     PAKAN: ADMIN DAN PETERNAK
                =================================== -->

                <?php if ($is_admin || $is_peternak): ?>

                <li class="nav-item">

                    <a class="nav-link <?= (strpos($current_uri, '/modul/pakan/') !== false) ? 'active' : ''; ?>"
                       href="<?= htmlspecialchars($base_url); ?>/modul/pakan/index.php">

                        <i class="bi bi-basket me-1"></i>
                        Pakan

                    </a>

                </li>

                <?php endif; ?>


                <!-- ==================================
                     LAPORAN: SEMUA ROLE
                =================================== -->

                <li class="nav-item">

                    <a class="nav-link <?= (strpos($current_uri, '/modul/laporan/') !== false) ? 'active' : ''; ?>"
                       href="<?= htmlspecialchars($base_url); ?>/modul/laporan/index.php">

                        <i class="bi bi-file-earmark-text me-1"></i>
                        Laporan

                    </a>

                </li>


                <!-- ==================================
                     INFORMASI PENGGUNA
                =================================== -->

                <li class="nav-item d-flex align-items-center ms-lg-2">

                    <span class="text-white small me-2">

                        <?= htmlspecialchars($nama_user); ?>
                        (<?= htmlspecialchars(ucfirst($role_user)); ?>)

                    </span>

                </li>


                <!-- ==================================
                     LOGOUT: SEMUA ROLE
                =================================== -->

                <li class="nav-item ms-lg-2">

                    <a class="btn btn-outline-light btn-sm mt-1"
                       href="<?= htmlspecialchars($base_url); ?>/logout.php"
                       onclick="return confirm('Apakah Anda yakin ingin logout?')">

                        <i class="bi bi-box-arrow-right me-1"></i>
                        Logout

                    </a>

                </li>

            </ul>

        </div>

    </div>

</nav>


<!-- ==========================================
     CONTAINER PEMBUNGKUS KONTEN UTAMA
========================================== -->

<div class="container my-4">
