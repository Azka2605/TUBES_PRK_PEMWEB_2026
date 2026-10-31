<?php
// public/index.php
// Front Controller: arahkan URL halaman ke View yang sesuai.

$routes = [
    '/' => 'dashboard.php',
    '/dashboard' => 'dashboard.php',
    '/lahan' => 'lahan.php',
    '/siklus' => 'siklus.php',
    '/inventaris' => 'inventaris.php',
    '/pekerja' => 'pekerja.php',
    '/panen' => 'panen.php',
];

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$view = $routes[$path] ?? null;

if ($view === null) {
    http_response_code(404);
    echo "Halaman tidak ditemukan";
    exit;
}

// TODO: Sesuaikan path dasar jika proyek dijalankan dalam subfolder.
require_once __DIR__ . "/../app/Views/" . $view;
