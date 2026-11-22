<?php
require __DIR__ . '/config.php'; require __DIR__ . '/functions.php'; require_login();
$pageTitle = 'Dashboard';
$total = (int)$pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
$active = (int)$pdo->query("SELECT COUNT(*) FROM students WHERE status='Aktif'")->fetchColumn();
$male = (int)$pdo->query("SELECT COUNT(*) FROM students WHERE jenis_kelamin='Laki-laki'")->fetchColumn();
$female = (int)$pdo->query("SELECT COUNT(*) FROM students WHERE jenis_kelamin='Perempuan'")->fetchColumn();
$teachers = (int)$pdo->query("SELECT COUNT(*) FROM teachers")->fetchColumn();
$classes = $pdo->query("SELECT kelas, COUNT(*) jumlah FROM students GROUP BY kelas ORDER BY kelas")->fetchAll();
$recent = $pdo->query("SELECT nomor_induk,nama_lengkap,kelas,status,created_at FROM students ORDER BY id DESC LIMIT 6")->fetchAll();
$events = $pdo->query("SELECT title,event_date,category FROM events WHERE event_date>=CURDATE() ORDER BY event_date LIMIT 5")->fetchAll();
$agenda = $pdo->query("SELECT title,event_date,academic_year,source FROM calendar_events WHERE event_date>=CURDATE() ORDER BY event_date LIMIT 5")->fetchAll();
$maxClassCount = 0;
foreach ($classes as $classRow) $maxClassCount = max($maxClassCount, (int)$classRow['jumlah']);
require __DIR__ . '/header.php';
?>
<div class="welcome"><div><span class="eyebrow light">SELAMAT DATANG</span><h1>Administrasi Madrasah</h1><p>Kelola data induk siswa kelas 1–6 dalam satu sistem.</p></div><a class="btn white" href="siswa_form.php">＋ Tambah Siswa</a></div>
<div class="stats"><article class="stat"><span>Total Seluruh Siswa</span><strong><?=number_format($total)?></strong><small><?=number_format($active)?> siswa aktif</small></article><article class="stat"><span>Siswa Laki-laki</span><strong><?=number_format($male)?></strong><small>Data terdaftar</small></article><article class="stat"><span>Siswa Perempuan</span><strong><?=number_format($female)?></strong><small>Data terdaftar</small></article><article class="stat"><span>Total Guru</span><strong><?=number_format($teachers)?></strong><small>Data pengajar</small></article></div>
<div class="panel"><div class="panel-head"><div><h2>Grafik Siswa per Kelas</h2><p>Visualisasi jumlah siswa berdasarkan kelas.</p></div></div><div class="class-grid"><?php for($i=1;$i<=6;$i++):$count=0;foreach($classes as $c){if((int)$c['kelas']===$i)$count=(int)$c['jumlah'];}$barWidth=$maxClassCount?round($count/$maxClassCount*100):0;?><div class="class-card"><span>KELAS</span><b><?=$i?></b><small><?=$count?> siswa</small><div class="bar-track" role="img" aria-label="<?=$count?> siswa"><i style="width:<?=$barWidth?>%"></i></div></div><?php endfor;?></div></div>
<div class="panel"><div class="panel-head"><div><h2>Data Terbaru</h2><p>Enam data siswa terakhir ditambahkan.</p></div><a href="siswa.php" class="text-link">Lihat semua →</a></div><div class="table-wrap"><table><thead><tr><th>Nomor Induk</th><th>Nama Siswa</th><th>Kelas</th><th>Status</th><th>Terdaftar</th></tr></thead><tbody><?php if(!$recent):?><tr><td colspan="5" class="empty">Belum ada data siswa.</td></tr><?php endif;?><?php foreach($recent as $r):?><tr><td><?=e($r['nomor_induk'])?></td><td><b><?=e($r['nama_lengkap'])?></b></td><td>Kelas <?=e((string)$r['kelas'])?></td><td><span class="status"><?=e($r['status'])?></span></td><td><?=e(date('d-m-Y',strtotime($r['created_at'])))?></td></tr><?php endforeach;?></tbody></table></div></div>
<div class="stats"><div class="panel"><h2>Kegiatan mendatang</h2><?php if(!$events):?><p>Belum ada kegiatan terjadwal.</p><?php endif;?><?php foreach($events as $event):?><p><b><?=e($event['title'])?></b><br><?=e($event['event_date']?date('d-m-Y',strtotime($event['event_date'])):'Tanggal belum ditentukan')?> · <?=e($event['category']??'')?></p><?php endforeach;?><a class="text-link" href="pages/kegiatan.php">Kelola kegiatan →</a></div><div class="panel"><h2>Agenda kalender</h2><?php if(!$agenda):?><p>Belum ada agenda kalender yang dimasukkan.</p><?php endif;?><?php foreach($agenda as $item):?><p><b><?=e($item['title'])?></b><br><?=e(date('d-m-Y',strtotime($item['event_date'])))?> · <?=e($item['academic_year'])?></p><?php endforeach;?><p>Masukkan kalender resmi dari dokumen Kemenag yang telah diverifikasi.</p><a class="text-link" href="pages/kalender.php">Kelola kalender →</a></div></div>
<?php require __DIR__ . '/footer.php'; ?>
