<?php
// app/Models/InventarisModel.php
// Tugas: Query SQL murni (PDO) untuk tabel inventaris.
// Diisi oleh: anggota yang mengerjakan fitur Inventaris.

class InventarisModel
{
    private $conn;
    private $table_name = "inventaris";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // TODO: method read() -> SELECT * FROM inventaris
    // TODO: method readSingle($id) -> SELECT * FROM inventaris WHERE id = ?
    // TODO: method create($data) -> INSERT INTO inventaris (...)
    // TODO: method update($id, $data) -> UPDATE inventaris SET ... WHERE id = ?
    // TODO: method delete($id) -> DELETE FROM inventaris WHERE id = ?
}
