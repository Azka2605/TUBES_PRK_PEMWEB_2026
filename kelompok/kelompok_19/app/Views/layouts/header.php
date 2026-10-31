<?php
// app/Views/layouts/header.php
// Tugas: Header HTML + Bootstrap CSS + CSS kustom.
// Diisi oleh: anggota yang mengerjakan UI layout.

// TODO: Tambahkan meta tag, title dinamis, link CSS Bootstrap & style.css
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title : 'KebunKu - Sistem Informasi Manajemen Agrikultur'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="d-flex" id="wrapper">
        <!-- Sidebar akan di-include di sini -->