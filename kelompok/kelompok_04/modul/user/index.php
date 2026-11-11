
<?php
// ==========================================
// 1. PROTEKSI SESSION
// ==========================================
require_once __DIR__ . '/../../includes/auth.check.php';

// ==========================================
// 2. KONEKSI DATABASE
// ==========================================
require_once __DIR__ . '/../../config/koneksi.php';

// ==========================================
// 3. PEMBATASAN HAK AKSES
// ==========================================
if (!isset($_SESSION['user']) ||
    $_SESSION['user']['role'] !== 'admin') {
    http_response_code(403);
    die("Anda tidak memiliki hak akses untuk halaman ini.");
}

// ==========================================
// 4. MENGAMBIL DATA USER
// ==========================================
$query = "SELECT id_user, username, nama_lengkap, role, created_at
          FROM users
          ORDER BY id_user DESC";

$result = mysqli_query($koneksi, $query);

if (!$result) {
    die("Gagal mengambil data pengguna: " . mysqli_error($koneksi));
}

// ==========================================
// 5. FUNGSI MENGAMANKAN OUTPUT
// ==========================================
function aman($data) {
    return htmlspecialchars((string) $data, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen User - Log Ternak K04</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3>Manajemen Pengguna</h3>
            <p class="text-muted">
                Kelola akun pengguna Log Ternak K04
            </p>
        </div>

        <a href="../../index.php" class="btn btn-secondary">
            Kembali ke Dashboard
        </a>
    </div>

    <?php if (isset($_GET['pesan']) && $_GET['pesan'] === 'tambah'): ?>
        <div class="alert alert-success">
            Pengguna berhasil ditambahkan.
        </div>
    <?php elseif (isset($_GET['pesan']) && $_GET['pesan'] === 'edit'): ?>
        <div class="alert alert-success">
            Data pengguna berhasil diperbarui.
        </div>
    <?php elseif (isset($_GET['pesan']) && $_GET['pesan'] === 'hapus'): ?>
        <div class="alert alert-success">
            Pengguna berhasil dihapus.
        </div>
    <?php endif; ?>

    <div class="card shadow-sm">

        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <strong>Daftar Pengguna</strong>

            <a href="tambah.php" class="btn btn-light btn-sm">
                + Tambah Pengguna
            </a>
        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered table-striped align-middle">

                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Username</th>
                            <th>Nama Lengkap</th>
                            <th>Role</th>
                            <th>Tanggal Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php if (mysqli_num_rows($result) > 0): ?>

                        <?php $no = 1; ?>

                        <?php while ($user = mysqli_fetch_assoc($result)): ?>

                            <tr>
                                <td><?= $no++; ?></td>

                                <td>
                                    <?= aman($user['username']); ?>
                                </td>

                                <td>
                                    <?= aman($user['nama_lengkap']); ?>
                                </td>

                                <td>
                                    <?php
                                    $warna = 'secondary';

                                    if ($user['role'] === 'admin') {
                                        $warna = 'danger';
                                    } elseif ($user['role'] === 'peternak') {
                                        $warna = 'success';
                                    } elseif ($user['role'] === 'dokter') {
                                        $warna = 'primary';
                                    }
                                    ?>

                                    <span class="badge bg-<?= $warna; ?>">
                                        <?= aman(ucfirst($user['role'])); ?>
                                    </span>
                                </td>

                                <td>
                                    <?= aman($user['created_at']); ?>
                                </td>

                                <td>
                                    <a href="edit.php?id=<?= (int) $user['id_user']; ?>"
                                       class="btn btn-warning btn-sm">
                                        Edit
                                    </a>

                                    <form action="hapus.php"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?');">

                                        <input type="hidden"
                                               name="id_user"
                                               value="<?= (int) $user['id_user']; ?>">

                                        <button type="submit"
                                                class="btn btn-danger btn-sm">
                                            Hapus
                                        </button>

                                    </form>
                                </td>
                            </tr>

                        <?php endwhile; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="6" class="text-center">
                                Belum ada data pengguna.
                            </td>
                        </tr>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
