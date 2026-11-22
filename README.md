# Dashboard Admin Madrasah Diniyah Wihdatul Muslimat

Aplikasi administrasi berbasis PHP 8, MySQL, PDO, HTML, CSS, dan JavaScript untuk data siswa kelas 1–6. Tidak ada data siswa/guru atau tanggal resmi kalender yang disertakan.

## Instalasi XAMPP

1. Pastikan folder tersedia di `C:\xampp\htdocs\madrasah_wihdatul_admin`. Pada workspace pengembangan ini, folder tersebut ditautkan ke folder sumber di Downloads agar perubahan tetap sama.
2. Buka **XAMPP Control Panel**, tekan **Start** untuk Apache dan MySQL, dan pastikan keduanya berstatus aktif.
3. Buat database `db_madrasah_wm` di phpMyAdmin (collation `utf8mb4_unicode_ci`) jika belum tersedia.
4. Sesuaikan `DB_HOST`, `DB_NAME`, `DB_USER`, dan `DB_PASS` pada `config.php`. Pengaturan bawaan cocok untuk XAMPP lokal standar.
5. Buka `http://localhost/madrasah_wihdatul_admin/install.php`, lalu buat akun administrator dengan kata sandi minimal 12 karakter.
6. Installer membuat atau memeriksa tabel serta menonaktifkan pembuatan akun ulang setelah admin pertama dibuat. Hapus `install.php` setelah instalasi sebagai lapisan keamanan tambahan.
7. Buka `http://localhost/madrasah_wihdatul_admin/login.php`.

**Jangan menjalankan `index.php` memakai tombol Run/Code Runner di VS Code.** Itu menjalankan PHP sebagai CLI, bukan sebagai halaman web Apache. Untuk aplikasi ini gunakan Apache XAMPP dan buka URL `http://localhost/madrasah_wihdatul_admin/` di browser; tidak memerlukan `npm run dev`.

### Jika aplikasi belum terbuka

- **404 / halaman lain:** periksa folder di dalam `C:\xampp\htdocs\madrasah_wihdatul_admin` dan pastikan Apache aktif.
- **Koneksi database gagal:** pastikan MySQL aktif, database sudah dibuat, dan kredensial pada `config.php` sesuai.
- **Belum bisa login:** buat akun admin satu kali melalui `install.php`; aplikasi tidak menyediakan akun contoh.

Alternatif: impor `database.sql` melalui phpMyAdmin. Skrip membuat database dan tabel saja; skrip tidak mengisi data contoh. Tetap gunakan installer untuk membuat akun administrator.

## Modul

- Data induk siswa dengan identitas dan data orang tua/wali, nomor induk otomatis `WM<TAHUN>-<URUT>`, validasi NIK 16 digit, foto, pencarian, filter, profil, kartu, ekspor CSV untuk Excel, dan cetak/simpan PDF melalui browser.
- Data guru dan wali kelas; kelas, tahun ajaran dan jadwal; absensi dan rekap; nilai, catatan perkembangan dan riwayat kenaikan kelas.
- Kegiatan dengan dokumentasi foto; agenda kalender pendidikan dengan kolom sumber dokumen Kemenag; fasilitas/inventaris dan struktur organisasi.
- Dashboard ringkasan, rekap grafik sederhana, kegiatan dan agenda kalender.
- Akun administrator/operator, CSRF, password hash, prepared statements PDO, validasi dan pembatasan unggahan.
- Backup SQL dapat diunduh administrator dari menu Laporan. Salin folder `uploads` secara terpisah untuk mengamankan foto. Simpan backup pada lokasi terenkripsi dengan akses terbatas.

## Kalender resmi

Kalender tidak diisi dengan tanggal asumsi. Operator harus memeriksa dokumen resmi Kementerian Agama, lalu memasukkan agenda beserta tahun ajaran, tanggal, dan sumber dokumennya.

## Keamanan dan pemeliharaan

- Gunakan HTTPS di hosting, kata sandi unik, serta akses administrator terbatas.
- Jangan menaruh folder proyek di lokasi publik selain document root web server yang dilindungi konfigurasi.
- Batasi akses backup dan data identitas anak; backup berisi data pribadi.
- File gambar dibatasi JPG/PNG/WEBP maksimal 2 MB dan folder unggahan menolak eksekusi PHP.
- Operator tidak dapat menghapus data atau mengelola akun. Administrator dapat mengelola akun dan menghapus data.
- Buat backup database dan folder unggahan secara berkala; uji pemulihan backup di lingkungan terpisah.

## Catatan

Ekspor/cetak PDF menggunakan dialog cetak browser (pilih “Save as PDF”). Ekspor Excel menggunakan CSV UTF-8. Pastikan ekstensi PHP `pdo_mysql` dan `fileinfo` aktif di XAMPP.
