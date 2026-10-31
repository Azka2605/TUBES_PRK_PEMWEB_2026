<?php
// app/Controllers/InventarisController.php
// Tugas: Menerima request, memanggil Model Inventaris, mengembalikan JSON atau halaman web.
// Diisi oleh: anggota yang mengerjakan fitur Inventaris.

require_once __DIR__ . "/../Config/Database.php";
require_once __DIR__ . "/../Models/InventarisModel.php";

class InventarisController
{
    private $model;

    public function __construct()
    {
        $database = new \App\Config\Database();
        $db = $database->getConnection();
        $this->model = new InventarisModel($db);
    }

    // TODO: method index() - ambil semua data inventaris
    // TODO: method show($id) - ambil satu inventaris berdasarkan id
    // TODO: method create() - tambah data inventaris baru
    // TODO: method update($id) - ubah data inventaris
    // TODO: method delete($id) - hapus data inventaris
}
