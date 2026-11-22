<aside class="sidebar"><div class="side-brand"><div class="brand-mark small">م</div><div><b>Madrasah Diniyah</b><small>Wihdatul Muslimat</small></div></div><nav>
<a href="<?=$appBase?>index.php">▦ Dashboard</a>
<a href="<?=$appBase?>siswa.php">♙ Data Siswa</a>
<a href="<?=$appBase?>siswa_form.php">＋ Tambah Siswa</a>
<a href="<?=$appBase?>pages/data_guru.php">♙ Data Guru</a>
<a href="<?=$appBase?>pages/data_kelas.php">▦ Data Kelas</a>
<a href="<?=$appBase?>pages/absensi.php">✓ Absensi</a>
<a href="<?=$appBase?>pages/nilai.php">✎ Nilai</a>
<a href="<?=$appBase?>pages/riwayat_kelas.php">↑ Riwayat Kelas</a>
<a href="<?=$appBase?>pages/kegiatan.php">◷ Kegiatan</a>
<a href="<?=$appBase?>pages/kalender.php">▣ Kalender</a>
<a href="<?=$appBase?>pages/fasilitas.php">⌂ Fasilitas</a>
<a href="<?=$appBase?>pages/struktur_organisasi.php">♧ Organisasi</a>
<a href="<?=$appBase?>pages/laporan.php">▤ Laporan</a>
<?php if(($_SESSION['admin_role']??'admin')==='admin'):?><a href="<?=$appBase?>pages/pengguna.php">⚙ Pengguna</a><?php endif;?>
<a href="<?=$appBase?>logout.php">↪ Keluar</a></nav><div class="side-foot">Menteng · Jakarta Pusat</div></aside>
