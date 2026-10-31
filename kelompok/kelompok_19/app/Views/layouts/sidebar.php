<?php
// app/Views/layouts/sidebar.php
// Tugas: Sidebar navigasi menu ke halaman lahan, siklus, inventaris, pekerja, panen.
// Diisi oleh: anggota yang mengerjakan UI layout.
?>
        <div class="bg-light border-end" id="sidebar-wrapper" style="width: 250px;">
            <div class="list-group list-group-flush p-3">
                <a href="/dashboard" class="list-group-item list-group-item-action bg-light">Dashboard</a>
                <a href="/lahan" class="list-group-item list-group-item-action">Lahan</a>
                <a href="/siklus" class="list-group-item list-group-item-action">Siklus</a>
                <a href="/inventaris" class="list-group-item list-group-item-action">Inventaris</a>
                <a href="/pekerja" class="list-group-item list-group-item-action">Pekerja</a>
                <a href="/panen" class="list-group-item list-group-item-action">Panen</a>
            </div>
        </div>
        <!-- Konten utama -->
        <div id="page-content-wrapper" class="w-100">