# KebunKu

Sistem Informasi Manajemen Agrikultur. Tugas besar Praktikum Pemrograman Web.

## Anggota Kelompok

| Nama | NIM | Fitur |
|------|-----|-------|
| TODO | TODO | TODO |

## Fitur

- [ ] Dashboard (statistik + cuaca API pihak ketiga)
- [ ] Lahan
- [ ] Siklus Tanam
- [ ] Inventaris
- [ ] Pekerja
- [ ] Panen

## Struktur Folder

```
app/
  Config/      Koneksi database + helper response
  Controllers/ Logika REST API
  Models/      Query SQL (PDO)
  Views/       Template halaman
database/
  schema.sql   Skema tabel + seeder
public/
  index.php    Router halaman
  api.php      Router REST API
  assets/      CSS & JS
```

## Cara Menjalankan

1. Salin folder `kelompok_19` ke `laragon/www/`.
2. Import `database/schema.sql` lewat phpMyAdmin atau:
   ```
   mysql -u root -p < database/schema.sql
   ```
3. Sesuaikan kredensial di `app/Config/Database.php`.
4. Buka `http://kelompok_19.test/` (atau `http://localhost/kelompok_19/public/`).

## Alur Kerja

1. Setiap anggota mengisi file sesuai fiturnya (controller, model, view, js).
2. Semua request data lewat `public/api.php` dan dibalas JSON.
3. Data ditampilkan lewat JS `fetch` tanpa reload halaman.