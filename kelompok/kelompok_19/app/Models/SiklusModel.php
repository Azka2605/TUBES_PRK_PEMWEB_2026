<?php
// app/Models/SiklusModel.php
// Tugas: Query SQL murni (PDO) untuk tabel siklus.
// Diisi oleh: anggota yang mengerjakan fitur Siklus.

class SiklusModel
{
    private $conn;
    private $table_name = "siklus";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // TODO: method read() -> SELECT * FROM siklus
    // TODO: method readSingle($id) -> SELECT * FROM siklus WHERE id = ?
    // TODO: method create($data) -> INSERT INTO siklus (...)
    // TODO: method update($id, $data) -> UPDATE siklus SET ... WHERE id = ?
    // TODO: method delete($id) -> DELETE FROM siklus WHERE id = ?
}
