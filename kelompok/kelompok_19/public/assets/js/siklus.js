// public/assets/js/siklus.js
// Tugas: AJAX Fetch CRUD Siklus ke /api/siklus
// Diisi oleh: anggota yang mengerjakan fitur Siklus.

const API_SIKLUS = "/api/siklus";

// TODO: loadData() - GET semua siklus lalu render ke #tabelSiklus tbody
// TODO: loadLahanOptions() - GET /api/lahan untuk isi #siklusLahanId
// TODO: simpanData(event) - POST/PUT berdasarkan #siklusId
// TODO: editData(id) - ambil satu data, isi form modal
// TODO: hapusData(id) - konfirmasi lalu DELETE
// TODO: resetFormSiklus() - kosongkan form modal

function resetFormSiklus() {
    document.getElementById("formSiklus").reset();
    document.getElementById("siklusId").value = "";
    document.getElementById("modalSiklusTitle").textContent = "Tambah Siklus";
}

// TODO: Panggil loadData() dan loadLahanOptions() saat halaman siap.
