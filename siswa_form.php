<?php
require __DIR__ . '/config.php'; require __DIR__ . '/functions.php'; require_login();
$id=(int)($_GET['id']??0);$data=[];$isEdit=false;
if($id){$s=$pdo->prepare('SELECT * FROM students WHERE id=?');$s->execute([$id]);$data=$s->fetch()?:[];if(!$data){http_response_code(404);exit('Data siswa tidak ditemukan.');}$isEdit=true;}
$pageTitle=$isEdit?'Edit Data Siswa':'Tambah Data Siswa';
$fields=[
'Identitas Siswa'=>[
['nomor_induk','Nomor Induk Madrasah *','text',true],['nisn','NISN','text'],['nama_lengkap','Nama Lengkap *','text',true],['nik','NIK Siswa (16 digit)','text'],['tempat_lahir','Tempat Lahir','text'],['tanggal_lahir','Tanggal Lahir','date'],['jenis_kelamin','Jenis Kelamin','select'],['kelas','Kelas','class'],['tahun_masuk','Tahun Masuk','number'],['status','Status Siswa','status']],
'Alamat Siswa'=>[['alamat','Alamat Lengkap','textarea'],['rt_rw','RT / RW','text'],['kelurahan','Kelurahan','text'],['kecamatan','Kecamatan','text'],['kota','Kota / Kabupaten','text']],
'Data Ayah'=>[['nama_ayah','Nama Ayah','text'],['nik_ayah','NIK Ayah (16 digit)','text'],['pekerjaan_ayah','Pekerjaan Ayah','text'],['telepon_ayah','Nomor Telepon Ayah','tel']],
'Data Ibu'=>[['nama_ibu','Nama Ibu','text'],['nik_ibu','NIK Ibu (16 digit)','text'],['pekerjaan_ibu','Pekerjaan Ibu','text'],['telepon_ibu','Nomor Telepon Ibu','tel']],
'Data Wali (jika berbeda)'=>[['nama_wali','Nama Wali','text'],['hubungan_wali','Hubungan dengan Siswa','text'],['telepon_wali','Nomor Telepon Wali','tel'],['alamat_wali','Alamat Wali','textarea']],
'Catatan'=>[['catatan','Catatan Tambahan','textarea']]
];
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 verify_csrf();
 $v=[];foreach(['nomor_induk','nisn','nama_lengkap','nik','tempat_lahir','tanggal_lahir','jenis_kelamin','alamat','rt_rw','kelurahan','kecamatan','kota','kelas','tahun_masuk','nama_ayah','nik_ayah','pekerjaan_ayah','telepon_ayah','nama_ibu','nik_ibu','pekerjaan_ibu','telepon_ibu','nama_wali','hubungan_wali','telepon_wali','alamat_wali','status','catatan'] as $key){$v[$key]=trim((string)($_POST[$key]??''));}
 if($v['nomor_induk']===''||$v['nama_lengkap']===''||!in_array($v['jenis_kelamin'],['Laki-laki','Perempuan'],true)||!in_array((int)$v['kelas'],range(1,6),true)){$error='Lengkapi Nomor Induk, Nama, Jenis Kelamin, dan Kelas dengan benar.';}
 foreach(['nik','nik_ayah','nik_ibu'] as $key){if($v[$key]!==''&&!preg_match('/^\d{16}$/',$v[$key]))$error='NIK harus terdiri dari 16 digit angka.';}
 if(!$error){
  try{
   $old=$data['foto']??null;$photo=save_student_photo($_FILES['foto']??[], $old);
   $cols=array_keys($v);$vals=array_values($v);$vals[]=$photo;
   if($isEdit){$sets=implode(',',array_map(fn($c)=>"$c=?",array_merge($cols,['foto'])));$vals[]=$id;$stmt=$pdo->prepare("UPDATE students SET $sets WHERE id=?");$stmt->execute($vals);}
   else{$colstr=implode(',',array_merge($cols,['foto']));$marks=implode(',',array_fill(0,count($cols)+1,'?'));$stmt=$pdo->prepare("INSERT INTO students($colstr) VALUES($marks)");$stmt->execute($vals);}
   redirect_with($isEdit?'Data siswa berhasil diperbarui.':'Data siswa berhasil disimpan.');
  }catch(Throwable $e){$error=$e instanceof PDOException?'Nomor Induk mungkin sudah digunakan. Periksa kembali.':$e->getMessage();}
 }
}
require __DIR__ . '/header.php';
?>
<div class="page-actions"><div><h1><?=$isEdit?'Edit':'Tambah'?> Data Siswa</h1><p>Kolom bertanda * wajib diisi. Umur dihitung otomatis dari tanggal lahir.</p></div><a class="btn soft" href="siswa.php">← Kembali</a></div>
<?php if($error):?><div class="alert error"><?=e($error)?></div><?php endif;?>
<form method="post" enctype="multipart/form-data"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>">
<div class="panel photo-panel"><div><?php if(!empty($data['foto'])):?><img class="photo-preview" src="uploads/foto_siswa/<?=e($data['foto'])?>" alt="Foto saat ini"><?php else:?><div class="photo-preview placeholder">FOTO</div><?php endif;?></div><div><h2>Foto Siswa</h2><p>Unggah foto formal JPG, PNG, atau WEBP. Maksimal 2 MB.</p><input type="file" name="foto" accept="image/jpeg,image/png,image/webp"></div></div>
<?php foreach($fields as $section=>$items):?><div class="panel form-panel"><h2><?=e($section)?></h2><div class="form-grid"><?php foreach($items as [$key,$label,$type]):?><div class="field <?=$type==='textarea'?'wide':''?>"><label for="<?=e($key)?>"><?=e($label)?></label>
<?php if($type==='textarea'):?><textarea id="<?=e($key)?>" name="<?=e($key)?>" rows="3"><?=e($data[$key]??$_POST[$key]??'')?></textarea>
<?php elseif($type==='select'):?><select id="<?=$key?>" name="<?=$key?>" required><option value="">Pilih</option><?php foreach(['Laki-laki','Perempuan'] as $op):?><option <?=$op===($data[$key]??$_POST[$key]??'')?'selected':''?>><?=$op?></option><?php endforeach;?></select>
<?php elseif($type==='class'):?><select id="<?=$key?>" name="<?=$key?>" required><?php for($i=1;$i<=6;$i++):?><option value="<?=$i?>" <?=(string)$i===(string)($data[$key]??$_POST[$key]??1)?'selected':''?>>Kelas <?=$i?></option><?php endfor;?></select>
<?php elseif($type==='status'):?><select id="<?=$key?>" name="<?=$key?>"><?php foreach(['Aktif','Lulus','Pindah','Tidak Aktif'] as $op):?><option <?=$op===($data[$key]??$_POST[$key]??'Aktif')?'selected':''?>><?=$op?></option><?php endforeach;?></select>
<?php else:?><input id="<?=e($key)?>" name="<?=e($key)?>" type="<?=e($type)?>" value="<?=e((string)($data[$key]??$_POST[$key]??''))?>" <?=in_array($key,['nomor_induk','nama_lengkap'],true)?'required':''?> <?=str_starts_with($key,'nik')?'inputmode="numeric" maxlength="16"':''?>><?php endif;?></div><?php endforeach;?></div></div><?php endforeach;?>
<div class="form-submit"><a class="btn soft" href="siswa.php">Batal</a><button class="btn primary">Simpan Data Siswa</button></div>
</form>
<?php require __DIR__ . '/footer.php'; ?>
