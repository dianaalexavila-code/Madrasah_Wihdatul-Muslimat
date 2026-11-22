PANDUAN INSTALASI - MADRASAH DINIYAH WIHDATUL MUSLIMAT

ISI PAKET
- Dashboard admin PHP + MySQL
- Login admin
- Data induk siswa kelas 1-6
- Upload foto siswa
- Biodata siswa dan data ayah/ibu/wali
- NIK, NISN, nomor induk madrasah
- Umur otomatis dari tanggal lahir
- Pencarian, filter kelas, edit, hapus, cetak daftar
- Cetak kartu identitas siswa

INSTALASI DI XAMPP
1. Ekstrak folder madrasah_wihdatul_admin ke C:\xampp\htdocs\
2. Jalankan Apache dan MySQL dari XAMPP.
3. Buka http://localhost/phpmyadmin/
4. Buat database bernama db_madrasah_wm dengan collation utf8mb4_unicode_ci.
5. Buka config.php. Untuk XAMPP standar, DB_HOST=localhost, DB_NAME=db_madrasah_wm, DB_USER=root, DB_PASS kosong.
6. Buka http://localhost/madrasah_wihdatul_admin/install.php
7. Buat akun admin dengan email dan kata sandi kuat minimal 12 karakter.
8. Setelah instalasi berhasil, hapus file install.php dari folder aplikasi.
9. Buka http://localhost/madrasah_wihdatul_admin/login.php

INSTALASI DI HOSTING
1. Buat database MySQL dan user database dari panel hosting.
2. Isi DB_HOST, DB_NAME, DB_USER, DB_PASS pada config.php sesuai data hosting.
3. Unggah isi folder aplikasi ke public_html atau subfolder domain.
4. Pastikan folder uploads/foto_siswa dapat ditulis oleh PHP (izin folder yang disarankan 0750/0755 sesuai hosting).
5. Buka /install.php, buat akun admin, lalu hapus install.php.
6. Aktifkan SSL/HTTPS dari panel hosting.
7. Uji tambah, edit, cari, cetak, dan hapus data sebelum digunakan.

CATATAN KEAMANAN
- Gunakan kata sandi unik dan minimal 12 karakter.
- Jangan bagikan akun admin kepada semua guru. Buat sistem hak akses terpisah sebelum menambah pengguna lain.
- Jangan mengirim NIK atau data anak melalui grup chat.
- Batasi akses aplikasi, aktifkan HTTPS, dan buat backup database berkala.
- Data NIK bersifat opsional pada formulir; isi hanya jika benar-benar diperlukan dan ada dasar/izin pengumpulan.
- Paket ini adalah fondasi modul data siswa, belum mencakup absensi, nilai, kalender Kemenag, data guru, dan laporan akademik.
