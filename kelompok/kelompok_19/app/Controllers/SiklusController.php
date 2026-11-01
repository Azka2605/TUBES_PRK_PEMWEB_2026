<?php
// app/Controllers/SiklusController.php
// Tugas: Menerima request, memanggil Model Siklus, mengembalikan JSON atau halaman web.
// Diisi oleh: anggota yang mengerjakan fitur Siklus.

require_once __DIR__ . "/../Config/Database.php";
require_once __DIR__ . "/../Models/SiklusModel.php";

class SiklusController
{
    private $model;

    public function __construct()
    {
        $database = new \App\Config\Database();
        $db = $database->getConnection();
        $this->model = new SiklusModel($db);
    }

    // TODO: method index() - ambil semua data siklus
    // TODO: method show($id) - ambil satu siklus berdasarkan id
    // TODO: method create() - tambah data siklus baru
    // TODO: method update($id) - ubah data siklus
    // TODO: method delete($id) - hapus data siklus
}
