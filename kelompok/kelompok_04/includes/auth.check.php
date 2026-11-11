
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}
