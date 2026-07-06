# Sistem Informasi Pondok Pesantren

Aplikasi web sederhana untuk manajemen pengumuman di pondok pesantren dengan dua role: **Admin** (mengelola pengumuman) dan **Wali Santri** (melihat pengumuman).

## Fitur

- Login multi-level (Admin / Wali Santri)
- CRUD pengumuman (Admin)
- Lihat pengumuman (Wali Santri)
- Kategori pengumuman: Umum, Akademik, Kegiatan, Keamanan

## Persyaratan

- XAMPP / PHP 7.4+
- MySQL / MariaDB

## Instalasi

1. Clone repositori ini ke `htdocs`:
   ```bash
   git clone https://github.com/username/pondok-pesantren.git
   ```

2. Import database:
   - Buka phpMyAdmin, buat database `db_pondok`
   - Import file `sql/database.sql`
   - Atau jalankan: `mysql -u root < sql/database.sql`

3. Salin konfigurasi:
   ```bash
   cp config.example.php config.php
   ```

4. Sesuaikan `config.php` jika perlu (default XAMPP: root tanpa password).

5. Akses di browser:
   ```
   http://localhost/pondok-pesantren
   ```

## Akun Demo

| Username | Password | Role  |
|----------|----------|-------|
| admin    | password | Admin |
| wali     | password | Wali  |

> **Catatan:** Ubah password segera setelah deploy ke production.
