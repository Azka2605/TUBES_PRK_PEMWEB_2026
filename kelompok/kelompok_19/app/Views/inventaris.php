<?php
// app/Views/inventaris.php
// Tugas: CRUD Inventaris (tabel + form modal).
// Diisi oleh: anggota yang mengerjakan fitur Inventaris.

$title = "Manajemen Inventaris";
require_once __DIR__ . "/layouts/header.php";
require_once __DIR__ . "/layouts/sidebar.php";
?>
            <div class="container-fluid p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h1>Data Inventaris</h1>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalInventaris" onclick="resetFormInventaris()">
                        Tambah Item
                    </button>
                </div>

                <div class="card">
                    <div class="card-body">
                        <table class="table table-striped" id="tabelInventaris">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Item</th>
                                    <th>Kategori</th>
                                    <th>Stok</th>
                                    <th>Satuan</th>
                                    <th>Harga Satuan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- TODO: Isi data via JS fetch ke API -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modal Form Inventaris -->
            <div class="modal fade" id="modalInventaris" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalInventarisTitle">Tambah Item</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form id="formInventaris">
                            <input type="hidden" name="id" id="inventarisId">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Nama Item</label>
                                    <input type="text" class="form-control" name="nama" id="inventarisNama" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Kategori</label>
                                    <select class="form-select" name="kategori" id="inventarisKategori" required>
                                        <option value="">Pilih...</option>
                                        <option value="Bibit">Bibit</option>
                                        <option value="Pupuk">Pupuk</option>
                                        <option value="Pestisida">Pestisida</option>
                                        <option value="Alat">Alat</option>
                                        <option value="Lainnya">Lainnya</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Stok</label>
                                    <input type="number" class="form-control" name="stok" id="inventarisStok" required min="0">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Satuan</label>
                                    <input type="text" class="form-control" name="satuan" id="inventarisSatuan" required placeholder="Contoh: kg, liter, pcs">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Harga Satuan</label>
                                    <input type="number" class="form-control" name="harga_satuan" id="inventarisHargaSatuan" required min="0" step="0.01">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <script src="/assets/js/inventaris.js"></script>
<?php
require_once __DIR__ . "/layouts/footer.php";
?>