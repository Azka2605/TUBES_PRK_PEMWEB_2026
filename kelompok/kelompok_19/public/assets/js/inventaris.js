// public/assets/js/inventaris.js
// Tugas: AJAX Fetch CRUD Inventaris ke /api/inventaris
// Diisi oleh: anggota yang mengerjakan fitur Inventaris.

const API_INVENTARIS = "/api/inventaris";

// TODO: loadData() - GET semua inventaris lalu render ke #tabelInventaris tbody
// TODO: simpanData(event) - POST/PUT berdasarkan #inventarisId
// TODO: editData(id) - ambil satu data, isi form modal
// TODO: hapusData(id) - konfirmasi lalu DELETE
// TODO: resetFormInventaris() - kosongkan form modal

function resetFormInventaris() {
    document.getElementById("formInventaris").reset();
    document.getElementById("inventarisId").value = "";
    document.getElementById("modalInventarisTitle").textContent = "Tambah Item";
}

// TODO: Panggil loadData() saat halaman siap.
