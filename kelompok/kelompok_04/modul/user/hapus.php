
<?php
// Memanggil autentikasi dan koneksi database
$allowed_roles = ['admin'];
require_once __DIR__ . '/../../includes/auth.check.php';
require_once __DIR__ . '/../../config/koneksi.php';

// Memastikan hanya admin yang dapat menghapus pengguna
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    http_response_code(403);
    die("Anda tidak memiliki hak akses.");
}

// Penghapusan hanya boleh melalui POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die("Metode permintaan tidak diizinkan.");
}

// Mengambil dan memvalidasi ID pengguna
$id_user = filter_input(INPUT_POST, 'id_user', FILTER_VALIDATE_INT);

if (!$id_user || $id_user < 1) {
    die("ID pengguna tidak valid.");
}

// Mengambil data pengguna yang akan dihapus
$stmt = mysqli_prepare(
    $koneksi,
    "SELECT id_user, username, role FROM users WHERE id_user = ?"
);
mysqli_stmt_bind_param($stmt, "i", $id_user);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$user) {
    die("Pengguna tidak ditemukan.");
}

// Mencegah admin menghapus akun sendiri
$id_login = $_SESSION['user']['id'] ?? $_SESSION['user']['id_user'] ?? null;

if ($id_login !== null && (int) $id_login === (int) $id_user) {
    die("Anda tidak dapat menghapus akun yang sedang digunakan.");
}

// Mencegah penghapusan admin terakhir
if ($user['role'] === 'admin') {
    $cek_admin = mysqli_query(
        $koneksi,
        "SELECT COUNT(*) AS jumlah FROM users WHERE role = 'admin'"
    );

    $data_admin = mysqli_fetch_assoc($cek_admin);

    if ((int) $data_admin['jumlah'] <= 1) {
        die("Admin terakhir tidak dapat dihapus.");
    }
}

// Menghapus pengguna menggunakan prepared statement
$hapus = mysqli_prepare(
    $koneksi,
    "DELETE FROM users WHERE id_user = ?"
);

mysqli_stmt_bind_param($hapus, "i", $id_user);

if (mysqli_stmt_execute($hapus)) {
    mysqli_stmt_close($hapus);

    header("Location: index.php?pesan=hapus");
    exit;
}

mysqli_stmt_close($hapus);

die("Gagal menghapus pengguna.");
?>
