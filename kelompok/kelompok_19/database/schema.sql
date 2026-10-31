-- database/schema.sql
-- Skema tabel + seeder data awal KebunKu.
-- Diisi oleh: seluruh anggota (koordinasi lewat ketua kelompok).

CREATE DATABASE IF NOT EXISTS kebunku;
USE kebunku;

-- TODO: tabel lahan
-- Kolom minimal: id, nama, lokasi, luas, jenis_tanah, created_at

-- TODO: tabel siklus
-- Kolom minimal: id, lahan_id (FK), nama_tanaman, tanggal_tanam,
--                tanggal_panen_estimasi, status, created_at

-- TODO: tabel inventaris
-- Kolom minimal: id, nama, kategori, stok, satuan, harga_satuan, created_at

-- TODO: tabel pekerja
-- Kolom minimal: id, nama, no_telepon, jabatan, gaji_pokok,
--                tanggal_bergabung, created_at

-- TODO: tabel panen
-- Kolom minimal: id, siklus_id (FK), tanggal_panen, jumlah_kg,
--                kualitas, catatan, created_at

-- TODO: seeder data awal (INSERT INTO ...)
