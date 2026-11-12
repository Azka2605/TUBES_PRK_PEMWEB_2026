
<?php
// ==========================================
// 1. PROTEKSI SESSION
// ==========================================
$allowed_roles = ['admin'];
require_once __DIR__ . '/../../includes/auth.check.php';

// ==========================================
// 2. KONEKSI DATABASE
// ==========================================
require_once __DIR__ . '/../../config/koneksi.php';

// ==========================================
// 3. PEMBATASAN AKSES ADMIN
// ==========================================
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    http_response_code(403);
    die("Anda tidak memiliki hak akses.");
}

// ==========================================
// 4. AMBIL ID PENGGUNA
// ==========================================
$id_user = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id_user || $id_user < 1) {
    die("ID pengguna tidak valid.");
}

// ==========================================
// 5. AMBIL DATA PENGGUNA
// ==========================================
$stmt = mysqli_prepare(
    $koneksi,
    "SELECT id_user, username, nama_lengkap, role
     FROM users
     WHERE id_user = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id_user);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if (!$user) {
    die("Data pengguna tidak ditemukan.");
}

// ==========================================
// 6. PROSES UPDATE
// ==========================================
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
    $role = $_POST['role'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username === '' || $nama_lengkap === '' || $role === '') {
        $error = "Username, nama lengkap, dan role wajib diisi.";
    } elseif (strlen($username) > 50 || strlen($nama_lengkap) > 100) {
        $error = "Username maksimal 50 karakter dan nama lengkap maksimal 100 karakter.";
    } elseif (!in_array($role, ['admin', 'peternak', 'dokter'], true)) {
        $error = "Role tidak valid.";
    } elseif ($password !== '' && strlen($password) < 8) {
        $error = "Password baru minimal 8 karakter.";
    } else {
        // Periksa apakah username digunakan pengguna lain
        $cek = mysqli_prepare(
            $koneksi,
            "SELECT id_user FROM users
             WHERE username = ? AND id_user != ?"
        );

        mysqli_stmt_bind_param($cek, "si", $username, $id_user);
        mysqli_stmt_execute($cek);
        mysqli_stmt_store_result($cek);

        if (mysqli_stmt_num_rows($cek) > 0) {
            $error = "Username sudah digunakan oleh pengguna lain.";
        }

        mysqli_stmt_close($cek);

        if ($error === '') {
            if ($password !== '') {
                // Update sekaligus mengganti password
                $password_hash = password_hash($password, PASSWORD_DEFAULT);

                $update = mysqli_prepare(
                    $koneksi,
                    "UPDATE users
                     SET username = ?, nama_lengkap = ?, role = ?, password = ?
                     WHERE id_user = ?"
                );

                mysqli_stmt_bind_param(
                    $update,
                    "ssssi",
                    $username,
                    $nama_lengkap,
                    $role,
                    $password_hash,
                    $id_user
                );
            } else {
                // Update tanpa mengganti password
                $update = mysqli_prepare(
                    $koneksi,
                    "UPDATE users
                     SET username = ?, nama_lengkap = ?, role = ?
                     WHERE id_user = ?"
                );

                mysqli_stmt_bind_param(
                    $update,
                    "sssi",
                    $username,
                    $nama_lengkap,
                    $role,
                    $id_user
                );
            }

            if (mysqli_stmt_execute($update)) {
                mysqli_stmt_close($update);
                header("Location: index.php?pesan=edit");
                exit;
            } else {
                $error = "Gagal memperbarui data pengguna.";
            }

            mysqli_stmt_close($update);
        }
    }

    // Pertahankan input jika validasi gagal
    $user['username'] = $username;
    $user['nama_lengkap'] = $nama_lengkap;
    $user['role'] = $role;
}

function aman($data) {
    return htmlspecialchars((string) $data, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pengguna - Log Ternak K04</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow-sm">

        <div class="card-header bg-warning">
            <h4 class="mb-0">Edit Pengguna</h4>
        </div>

        <div class="card-body">

            <?php if ($error !== ''): ?>
                <div class="alert alert-danger">
                    <?= aman($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST">

                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text"
                           name="username"
                           class="form-control"
                           maxlength="50"
                           value="<?= aman($user['username']); ?>"
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text"
                           name="nama_lengkap"
                           class="form-control"
                           maxlength="100"
                           value="<?= aman($user['nama_lengkap']); ?>"
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password Baru</label>
                    <input type="password"
                           name="password"
                           class="form-control"
                           minlength="8"
                           placeholder="Kosongkan jika tidak ingin mengganti password">
                    <small class="text-muted">
                        Isi minimal 8 karakter jika ingin mengganti password.
                    </small>
                </div>

                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select" required>
                        <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : ''; ?>>
                            Admin
                        </option>
                        <option value="peternak" <?= $user['role'] === 'peternak' ? 'selected' : ''; ?>>
                            Peternak
                        </option>
                        <option value="dokter" <?= $user['role'] === 'dokter' ? 'selected' : ''; ?>>
                            Dokter
                        </option>
                    </select>
                </div>

                <button type="submit" class="btn btn-warning">
                    Simpan Perubahan
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
