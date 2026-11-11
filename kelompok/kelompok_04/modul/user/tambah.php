
<?php
// 1. Proteksi session
require_once __DIR__ . '/../../includes/auth.check.php';

// 2. Koneksi database
require_once __DIR__ . '/../../config/koneksi.php';

// 3. Batasi akses hanya untuk admin
if ($_SESSION['user']['role'] !== 'admin') {
    http_response_code(403);
    die("Anda tidak memiliki hak akses.");
}

$error = "";

// 4. Proses tambah pengguna
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    // Validasi input
    if ($username === '' || $nama_lengkap === '' || $password === '' || $role === '') {
        $error = "Semua kolom wajib diisi.";
    } elseif (!in_array($role, ['admin', 'peternak', 'dokter'], true)) {
        $error = "Role tidak valid.";
    } elseif (strlen($password) < 8) {
        $error = "Password minimal 8 karakter.";
    } else {

        // Cek username sudah digunakan
        $cek = mysqli_prepare(
            $koneksi,
            "SELECT id_user FROM users WHERE username = ?"
        );

        mysqli_stmt_bind_param($cek, "s", $username);
        mysqli_stmt_execute($cek);
        mysqli_stmt_store_result($cek);

        if (mysqli_stmt_num_rows($cek) > 0) {
            $error = "Username sudah digunakan.";
        } else {

            // Hash password
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // Simpan pengguna
            $stmt = mysqli_prepare(
                $koneksi,
                "INSERT INTO users (username, password, nama_lengkap, role)
                 VALUES (?, ?, ?, ?)"
            );

            mysqli_stmt_bind_param(
                $stmt,
                "ssss",
                $username,
                $password_hash,
                $nama_lengkap,
                $role
            );

            if (mysqli_stmt_execute($stmt)) {
                header("Location: index.php?pesan=tambah");
                exit;
            } else {
                $error = "Gagal menambahkan pengguna.";
            }

            mysqli_stmt_close($stmt);
        }

        mysqli_stmt_close($cek);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pengguna</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5">

    <div class="card shadow-sm">

        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Tambah Pengguna</h4>
        </div>

        <div class="card-body">

            <?php if ($error !== ''): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <form method="POST">

                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text"
                           name="username"
                           class="form-control"
                           maxlength="50"
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text"
                           name="nama_lengkap"
                           class="form-control"
                           maxlength="100"
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password"
                           name="password"
                           class="form-control"
                           minlength="8"
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select" required>
                        <option value="">-- Pilih Role --</option>
                        <option value="admin">Admin</option>
                        <option value="peternak">Peternak</option>
                        <option value="dokter">Dokter</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">
                    Simpan Pengguna
                </button>

                <a href="index.php" class="btn btn-secondary">
                    Kembali
                </a>

            </form>

        </div>
    </div>

</div>

</body>
</html>
