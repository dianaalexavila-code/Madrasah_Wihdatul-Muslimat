<?php
require __DIR__ . '/config.php'; require __DIR__ . '/functions.php'; require_login();
$pageTitle = 'Dashboard';
$total = (int)$pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
$active = (int)$pdo->query("SELECT COUNT(*) FROM students WHERE status='Aktif'")->fetchColumn();
$classes = $pdo->query("SELECT kelas, COUNT(*) jumlah FROM students GROUP BY kelas ORDER BY kelas")->fetchAll();
$recent = $pdo->query("SELECT nomor_induk,nama_lengkap,kelas,status,created_at FROM students ORDER BY id DESC LIMIT 6")->fetchAll();
require __DIR__ . '/header.php';
?>
<div class="welcome"><div><span class="eyebrow light">SELAMAT DATANG</span><h1>Administrasi Madrasah</h1><p>Kelola data induk siswa kelas 1–6 dalam satu sistem.</p></div><a class="btn white" href="siswa_form.php">＋ Tambah Siswa</a></div>
<div class="stats"><article class="stat"><span>Total Siswa</span><strong><?=number_format($total)?></strong><small>Seluruh data terdaftar</small></article><article class="stat"><span>Siswa Aktif</span><strong><?=number_format($active)?></strong><small>Status aktif</small></article><article class="stat"><span>Jumlah Kelas</span><strong>6</strong><small>Kelas 1 sampai 6</small></article></div>
<div class="panel"><div class="panel-head"><div><h2>Rekap Siswa per Kelas</h2><p>Jumlah siswa berdasarkan kelas yang sudah terdaftar.</p></div></div><div class="class-grid"><?php for($i=1;$i<=6;$i++):$count=0;foreach($classes as $c){if((int)$c['kelas']===$i)$count=(int)$c['jumlah'];}?><div class="class-card"><span>KELAS</span><b><?=$i?></b><small><?=$count?> siswa</small></div><?php endfor;?></div></div>
<div class="panel"><div class="panel-head"><div><h2>Data Terbaru</h2><p>Enam data siswa terakhir ditambahkan.</p></div><a href="siswa.php" class="text-link">Lihat semua →</a></div><div class="table-wrap"><table><thead><tr><th>Nomor Induk</th><th>Nama Siswa</th><th>Kelas</th><th>Status</th><th>Terdaftar</th></tr></thead><tbody><?php if(!$recent):?><tr><td colspan="5" class="empty">Belum ada data siswa.</td></tr><?php endif;?><?php foreach($recent as $r):?><tr><td><?=e($r['nomor_induk'])?></td><td><b><?=e($r['nama_lengkap'])?></b></td><td>Kelas <?=e((string)$r['kelas'])?></td><td><span class="status"><?=e($r['status'])?></span></td><td><?=e(date('d-m-Y',strtotime($r['created_at'])))?></td></tr><?php endforeach;?></tbody></table></div></div>
<?php require __DIR__ . '/footer.php'; ?>
