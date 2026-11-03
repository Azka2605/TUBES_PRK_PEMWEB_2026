<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_log_ternak_k04";

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi Database Gagal: " . mysqli_connect_error());
}
?>