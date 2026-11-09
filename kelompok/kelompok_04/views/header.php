<?php
// Pastikan session aktif di setiap halaman
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$current_uri = $_SERVER['REQUEST_URI'];
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

<!-- Navbar Utama -->
<nav class="navbar navbar-expand-lg navbar-dark bg-success shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="<?= $base_url; ?>/index.php">
      <i class="bi bi-box-seam me-2"></i>Log Ternak K04
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link <?= (strpos($current_uri, '/modul/') === false) ? 'active' : ''; ?>" href="<?= $base_url; ?>/index.php">
            <i class="bi bi-speedometer2 me-1"></i> Dashboard
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= (strpos($current_uri, '/modul/user/') !== false) ? 'active' : ''; ?>" href="<?= $base_url; ?>/modul/user/index.php">
            <i class="bi bi-people me-1"></i> User
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= (strpos($current_uri, '/modul/kandang/') !== false) ? 'active' : ''; ?>" href="<?= $base_url; ?>/modul/kandang/index.php">
            <i class="bi bi-house-door me-1"></i> Kandang
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= (strpos($current_uri, '/modul/ternak/') !== false) ? 'active' : ''; ?>" href="<?= $base_url; ?>/modul/ternak/index.php">
            <i class="bi bi-box-seam me-1"></i> Ternak
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= (strpos($current_uri, '/modul/kesehatan/') !== false) ? 'active' : ''; ?>" href="<?= $base_url; ?>/modul/kesehatan/index.php">
            <i class="bi bi-heart-pulse me-1"></i> Kesehatan
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= (strpos($current_uri, '/modul/pakan/') !== false) ? 'active' : ''; ?>" href="<?= $base_url; ?>/modul/pakan/index.php">
            <i class="bi bi-basket me-1"></i> Pakan
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= (strpos($current_uri, '/modul/laporan/') !== false) ? 'active' : ''; ?>" href="<?= $base_url; ?>/modul/laporan/index.php">
            <i class="bi bi-file-earmark-text me-1"></i> Laporan
          </a>
        </li>
        <li class="nav-item ms-lg-2">
          <a class="btn btn-outline-light btn-sm mt-1" href="<?= $base_url; ?>/logout.php">
            <i class="bi bi-box-arrow-right me-1"></i> Logout
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- Container Pembungkus Konten Utama -->
<div class="container my-4">