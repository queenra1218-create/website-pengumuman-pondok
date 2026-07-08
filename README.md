NAMA  : VERA FAZYIRA
NIM   : 101230069
KELAS : TF 23 C
# Sistem Informasi Pondok Pesantren

Aplikasi web sederhana berbasis PHP native untuk mengelola pengumuman internal di pondok pesantren dengan akses berdasarkan peran pengguna.

## Modul dan Fitur

### 1. Modul Autentikasi
- Login dan logout pengguna
- Sistem session untuk menjaga status login
- Pembagian akses berdasarkan role: Admin dan Wali Santri

### 2. Modul Pengumuman
- Admin dapat menambahkan pengumuman
- Admin dapat menghapus pengumuman
- Wali Santri dapat melihat daftar pengumuman
- Kategori pengumuman: Umum, Akademik, Kegiatan, Keamanan

### 3. Modul Dashboard
- Tampilan dashboard yang berbeda sesuai role pengguna
- Badge role untuk mempermudah identifikasi akun
- Informasi tambahan untuk pengguna Wali Santri

### 4. Modul Basis Data
- Tabel pengguna (`user`)
- Tabel pengumuman (`pengumuman`)
- Konfigurasi koneksi database melalui file konfigurasi

## Stack Teknologi yang Digunakan

- Bahasa pemrograman: PHP
- Database: MySQL / MariaDB
- Frontend: HTML, CSS
- Server lokal: XAMPP
- Koneksi database: MySQLi
- Arsitektur: PHP native tanpa framework

## Persyaratan Sistem

- XAMPP / PHP 7.4+
- MySQL / MariaDB
- Browser modern

## Instalasi

1. Clone repositori ini ke folder htdocs:
   ```bash
   git clone https://github.com/username/pondok-pesantren.git
   ```

2. Jalankan Apache dan MySQL melalui XAMPP.

3. Import database:
   - Buka phpMyAdmin, buat database `db_pondok`
   - Import file `sql/database.sql`
   - Atau jalankan:
   ```bash
   mysql -u root < sql/database.sql
   ```

4. Salin file konfigurasi:
   ```bash
   cp config.example.php config.php
   ```

5. Sesuaikan isi `config.php` jika diperlukan sesuai pengaturan local Anda.

6. Akses aplikasi melalui browser:
   ```text
   http://localhost/pondok-pesantren
   ```

## Cara Menjalankan di Localhost

1. Pastikan XAMPP sudah berjalan dan Apache serta MySQL aktif.
2. Tempatkan folder project ini di direktori `htdocs` pada XAMPP.
3. Buka browser lalu akses:
   ```text
   http://localhost/pondok-pesantren
   ```
4. Jika database belum dibuat, import file `sql/database.sql` terlebih dahulu.
5. Login menggunakan akun demo berikut:
   - Admin: `admin` / `123`
   - Wali Santri: `wali` / `123`

## Struktur Folder

- `index.php` → halaman utama aplikasi
- `config.php` → konfigurasi database
- `config.example.php` → contoh konfigurasi
- `sql/database.sql` → struktur database dan data awal
- `README.md` → dokumentasi proyek

## Catatan

Aplikasi ini masih dikembangkan secara sederhana dan cocok untuk kebutuhan pengumuman internal pondok pesantren.
