<?php
require __DIR__ . '/config.php'; require __DIR__ . '/functions.php'; require_login();
$pageTitle='Data Siswa';
$q=trim($_GET['q']??''); $kelas=(int)($_GET['kelas']??0);$status=trim($_GET['status']??'');
$sql="SELECT * FROM students WHERE 1=1"; $params=[];
if($q!==''){ $sql.=" AND (nama_lengkap LIKE ? OR nomor_induk LIKE ? OR nisn LIKE ?)"; $like="%$q%"; array_push($params,$like,$like,$like); }
if($kelas>=1 && $kelas<=6){$sql.=" AND kelas=?";$params[]=$kelas;}
if(in_array($status,['Aktif','Lulus','Pindah','Tidak Aktif'],true)){$sql.=" AND status=?";$params[]=$status;}
$sql.=" ORDER BY kelas,nama_lengkap";
$stmt=$pdo->prepare($sql);$stmt->execute($params);$rows=$stmt->fetchAll();
$flash=$_SESSION['flash']??null;unset($_SESSION['flash']);
require __DIR__ . '/header.php';
?>
<div class="page-actions"><div><h1>Data Induk Siswa</h1><p>Kelola biodata siswa Madrasah Diniyah kelas 1–6.</p></div><a class="btn primary" href="siswa_form.php">＋ Tambah Siswa</a></div>
<?php if($flash):?><div class="alert <?=e($flash['type'])?>"><?=e($flash['message'])?></div><?php endif;?>
<form class="filters" method="get"><input name="q" value="<?=e($q)?>" placeholder="Cari nama, nomor induk, atau NISN..."><select name="kelas"><option value="0">Semua kelas</option><?php for($i=1;$i<=6;$i++):?><option value="<?=$i?>" <?=$kelas===$i?'selected':''?>>Kelas <?=$i?></option><?php endfor;?></select><select name="status"><option value="">Semua status</option><?php foreach(['Aktif','Lulus','Pindah','Tidak Aktif'] as $s):?><option value="<?=e($s)?>" <?=$status===$s?'selected':''?>><?=e($s)?></option><?php endforeach;?></select><button class="btn dark">Cari</button><a class="btn soft" href="siswa.php">Reset</a></form>
<div class="panel"><div class="panel-head"><div><h2>Daftar Siswa</h2><p><?=count($rows)?> data ditemukan</p></div><div><a class="btn soft" href="pages/laporan.php?export=siswa">Ekspor Excel</a> <button class="btn soft" onclick="window.print()">Cetak / Simpan PDF</button></div></div><div class="table-wrap"><table><thead><tr><th>Foto</th><th>Nomor Induk</th><th>Nama Lengkap</th><th>Kelas</th><th>Umur</th><th>Wali / Kontak</th><th>Status</th><th>Aksi</th></tr></thead><tbody>
<?php if(!$rows):?><tr><td colspan="8" class="empty">Data belum ditemukan. Klik Tambah Siswa untuk memulai.</td></tr><?php endif;?>
<?php foreach($rows as $r):?><tr><td><?php if($r['foto']):?><img class="avatar" src="uploads/foto_siswa/<?=e($r['foto'])?>" alt="Foto siswa"><?php else:?><div class="avatar placeholder">—</div><?php endif;?></td><td><?=e($r['nomor_induk'])?></td><td><b><a href="pages/detail_siswa.php?id=<?=(int)$r['id']?>"><?=e($r['nama_lengkap'])?></a></b><small class="subline"><?=e($r['nik']?'••••••••••••'.substr($r['nik'],-4):'NIK belum diisi')?></small></td><td>Kelas <?=e((string)$r['kelas'])?></td><td><?=e(age_from_date($r['tanggal_lahir']))?></td><td><?=e($r['nama_wali']?:$r['nama_ayah']?:$r['nama_ibu']?:'-')?><small class="subline"><?=e($r['telepon_wali']?:$r['telepon_ayah']?:$r['telepon_ibu']?:'-')?></small></td><td><span class="status"><?=e($r['status'])?></span></td><td class="actions"><a href="siswa_form.php?id=<?=(int)$r['id']?>">Edit</a><a href="kartu.php?id=<?=(int)$r['id']?>" target="_blank">Kartu</a><form method="post" action="hapus_siswa.php" onsubmit="return confirm('Yakin ingin menghapus data siswa ini?')"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><input type="hidden" name="id" value="<?=(int)$r['id']?>"><button class="link-danger">Hapus</button></form></td></tr><?php endforeach;?>
</tbody></table></div></div>
<?php require __DIR__ . '/footer.php'; ?>
