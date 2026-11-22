<?php
require __DIR__ . '/../config.php';
require __DIR__ . '/../functions.php';
require_login();

$definitions = [
    'data_guru' => ['title'=>'Data Guru','table'=>'teachers','icon'=>'♙','fields'=>[
        'name'=>['Nama lengkap','text',true], 'nik'=>['NIK (16 digit)','text',false,'nik'],
        'phone'=>['Nomor telepon','tel'], 'address'=>['Alamat','textarea'],
        'education'=>['Pendidikan terakhir','text'], 'position'=>['Jabatan','text'],
        'homeroom_class'=>['Wali kelas (1–6)','class'], 'subjects'=>['Mata pelajaran','text'],
        'photo'=>['Foto guru','file']]],
    'data_kelas' => ['title'=>'Data Kelas','table'=>'classes','icon'=>'▦','fields'=>[
        'class_number'=>['Kelas (1–6)','class',true], 'academic_year'=>['Tahun ajaran','text',true],
        'room'=>['Ruang kelas','text'], 'teacher_id'=>['Wali kelas','teacher'], 'schedule'=>['Jadwal pelajaran','textarea']]],
    'absensi' => ['title'=>'Absensi Harian','table'=>'attendance','icon'=>'✓','fields'=>[
        'student_id'=>['Siswa','student',true], 'attendance_date'=>['Tanggal','date',true],
        'status'=>['Kehadiran','enum',['Hadir','Izin','Sakit','Alpa']], 'note'=>['Catatan','textarea']]],
    'nilai' => ['title'=>'Nilai & Perkembangan','table'=>'grades','icon'=>'✎','fields'=>[
        'student_id'=>['Siswa','student',true], 'subject'=>['Mata pelajaran','text',true],
        'term'=>['Semester / periode','text',true], 'score'=>['Nilai (0–100)','number',false,'score'],
        'development_note'=>['Catatan perkembangan','textarea']]],
    'riwayat_kelas' => ['title'=>'Riwayat Kenaikan Kelas','table'=>'promotions','icon'=>'↑','fields'=>[
        'student_id'=>['Siswa','student',true], 'from_class'=>['Dari kelas (1–6)','class',true],
        'to_class'=>['Ke kelas (1–6)','class',true], 'academic_year'=>['Tahun ajaran','text',true],
        'promotion_date'=>['Tanggal keputusan','date'], 'note'=>['Catatan','textarea']]],
    'kegiatan' => ['title'=>'Kegiatan Madrasah','table'=>'events','icon'=>'◷','fields'=>[
        'title'=>['Nama kegiatan','text',true], 'category'=>['Jenis kegiatan','enum',['Kegiatan Ramadan','Pesantren Kilat','Peringatan Hari Besar Islam','Porsadin','Lomba Keagamaan','Kegiatan Belajar','Lainnya']],
        'event_date'=>['Tanggal','date'], 'location'=>['Lokasi','text'], 'description'=>['Laporan / deskripsi','textarea'],
        'photo'=>['Dokumentasi foto','file']]],
    'kalender' => ['title'=>'Kalender Pendidikan','table'=>'calendar_events','icon'=>'▣','fields'=>[
        'title'=>['Agenda (masukkan dari sumber resmi)','text',true], 'academic_year'=>['Tahun ajaran','text',true],
        'event_date'=>['Tanggal resmi','date',true], 'end_date'=>['Tanggal selesai (opsional)','date'],
        'source'=>['Sumber / nomor dokumen Kemenag','text'], 'note'=>['Catatan','textarea']]],
    'fasilitas' => ['title'=>'Fasilitas & Inventaris','table'=>'facilities','icon'=>'⌂','fields'=>[
        'name'=>['Nama fasilitas / barang','text',true], 'category'=>['Kategori','text'],
        'quantity'=>['Jumlah','number'], 'condition'=>['Kondisi','enum',['Baik','Perlu Perbaikan','Rusak']],
        'location'=>['Lokasi','text'], 'note'=>['Catatan','textarea'], 'photo'=>['Foto fasilitas','file']]],
    'struktur_organisasi' => ['title'=>'Struktur Organisasi','table'=>'organization','icon'=>'♧','fields'=>[
        'role'=>['Jabatan','text',true], 'person_name'=>['Nama','text',true],
        'teacher_id'=>['Data guru terkait (opsional)','teacher'], 'sort_order'=>['Urutan tampilan','number'],
        'note'=>['Keterangan','textarea']]],
];
$module = $_GET['module'] ?? '';
if (!isset($definitions[$module])) { http_response_code(404); exit('Modul tidak ditemukan.'); }
$definition = $definitions[$module];
$table = $definition['table'];
$fields = $definition['fields'];
$error = '';
$message = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

function module_options(PDO $pdo, string $type): array {
    if ($type === 'student') return $pdo->query("SELECT id, CONCAT(nama_lengkap, ' — kelas ', kelas, ' (', nomor_induk, ')') label FROM students ORDER BY nama_lengkap")->fetchAll();
    if ($type === 'teacher') return $pdo->query("SELECT id, name label FROM teachers ORDER BY name")->fetchAll();
    return [];
}
function module_value(array $row, string $key): string { return (string)($row[$key] ?? ''); }
function module_label(array $field): string { return (string)$field[0]; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);
    $oldPhoto = null;
    $newPhoto = null;
    try {
        if ($action === 'delete' && $id > 0) {
            require_admin();
            $deletedPhoto = null;
            if (isset($fields['photo'])) {
                $photoQuery = $pdo->prepare("SELECT photo FROM `$table` WHERE id=?");
                $photoQuery->execute([$id]);
                $deletedPhoto = $photoQuery->fetchColumn() ?: null;
            }
            $delete = $pdo->prepare("DELETE FROM `$table` WHERE id=?");
            $delete->execute([$id]);
            if ($deletedPhoto && is_file(__DIR__ . '/../uploads/dokumen/' . basename($deletedPhoto))) unlink(__DIR__ . '/../uploads/dokumen/' . basename($deletedPhoto));
            $_SESSION['flash'] = ['message'=>'Data berhasil dihapus.','type'=>'success'];
            header('Location: manage.php?module=' . rawurlencode($module)); exit;
        }
        if ($action === 'save') {
            $values = [];
            foreach ($fields as $key => $field) {
                if ($field[1] === 'file') continue;
                $value = trim((string)($_POST[$key] ?? ''));
                $required = (bool)($field[2] ?? false);
                if ($required && $value === '') throw new RuntimeException(module_label($field) . ' wajib diisi.');
                if ($value !== '' && isset($field[3]) && $field[3] === 'nik' && !preg_match('/^\d{16}$/', $value)) throw new RuntimeException('NIK harus terdiri dari 16 digit angka.');
                if ($value !== '' && isset($field[3]) && $field[3] === 'score' && ((float)$value < 0 || (float)$value > 100)) throw new RuntimeException('Nilai harus berada pada rentang 0–100.');
                if ($value !== '' && $field[1] === 'number' && !is_numeric($value)) throw new RuntimeException(module_label($field) . ' harus berupa angka.');
                if ($field[1] === 'enum' && !in_array($value, $field[2], true)) throw new RuntimeException('Pilihan ' . strtolower(module_label($field)) . ' tidak valid.');
                if ($field[1] === 'class' && $value !== '' && !in_array((int)$value, range(1,6), true)) throw new RuntimeException('Kelas harus antara 1 sampai 6.');
                $values[$key] = $value === '' ? null : $value;
            }
            if (isset($fields['photo'])) {
                $currentPhoto = null;
                if ($id > 0) {
                    $current = $pdo->prepare("SELECT photo FROM `$table` WHERE id=?");
                    $current->execute([$id]);
                    $currentPhoto = $current->fetchColumn() ?: null;
                }
                $oldPhoto = $currentPhoto;
                $newPhoto = save_module_photo($_FILES['photo'] ?? [], $currentPhoto);
                $values['photo'] = $newPhoto;
            }
            if ($id > 0) {
                $set = implode(',', array_map(static fn($key) => "`$key`=?", array_keys($values)));
                $values[] = $id;
                $stmt = $pdo->prepare("UPDATE `$table` SET $set WHERE id=?");
            } else {
                $columns = implode(',', array_map(static fn($key) => "`$key`", array_keys($values)));
                $marks = implode(',', array_fill(0, count($values), '?'));
                $stmt = $pdo->prepare("INSERT INTO `$table` ($columns) VALUES ($marks)");
            }
            $stmt->execute(array_values($values));
            if ($oldPhoto && isset($values['photo']) && $values['photo'] !== $oldPhoto && is_file(__DIR__ . '/../uploads/dokumen/' . basename($oldPhoto))) {
                unlink(__DIR__ . '/../uploads/dokumen/' . basename($oldPhoto));
            }
            $_SESSION['flash'] = ['message'=>'Data berhasil disimpan.','type'=>'success'];
            header('Location: manage.php?module=' . rawurlencode($module)); exit;
        }
    } catch (Throwable $exception) {
        if ($newPhoto && $newPhoto !== $oldPhoto && is_file(__DIR__ . '/../uploads/dokumen/' . basename($newPhoto))) unlink(__DIR__ . '/../uploads/dokumen/' . basename($newPhoto));
        $error = $exception instanceof PDOException ? 'Data tidak dapat disimpan. Periksa isian dan data terkait.' : $exception->getMessage();
    }
}

$editing = null;
if (isset($_GET['edit'])) {
    $edit = $pdo->prepare("SELECT * FROM `$table` WHERE id=?");
    $edit->execute([(int)$_GET['edit']]);
    $editing = $edit->fetch() ?: null;
    if (!$editing) { http_response_code(404); exit('Data tidak ditemukan.'); }
}
$rows = $pdo->query("SELECT t.* FROM `$table` t ORDER BY t.id DESC LIMIT 500")->fetchAll();
$pageTitle = $definition['title'];
require __DIR__ . '/../header.php';
?>
<div class="page-actions"><div><h1><?=e($definition['title'])?></h1><p>Kelola dan simpan data secara langsung di database madrasah.</p></div></div>
<?php if($message):?><div class="alert <?=e($message['type'])?>"><?=e($message['message'])?></div><?php endif;?>
<?php if($error):?><div class="alert error"><?=e($error)?></div><?php endif;?>
<div class="panel form-panel"><h2><?=$editing?'Edit data':'Tambah data'?></h2>
<form method="post" enctype="multipart/form-data"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?=(int)($editing['id']??0)?>">
<div class="form-grid"><?php foreach($fields as $key=>$field):$type=$field[1];$value=module_value($editing??$_POST,$key);$required=(bool)($field[2]??false);?><div class="field <?=$type==='textarea'?'wide':''?>"><label for="<?=e($key)?>"><?=e($field[0])?><?=$required?' *':''?></label>
<?php if($type==='textarea'):?><textarea name="<?=e($key)?>" id="<?=e($key)?>" rows="3"><?=$value!==''?e($value):''?></textarea>
<?php elseif($type==='enum'):?><select name="<?=e($key)?>" id="<?=e($key)?>" required><?php foreach($field[2] as $option):?><option value="<?=e($option)?>" <?=$value===$option?'selected':''?>><?=e($option)?></option><?php endforeach;?></select>
<?php elseif($type==='class'):?><select name="<?=e($key)?>" id="<?=e($key)?>" <?=$required?'required':''?>><option value="">Pilih kelas</option><?php for($i=1;$i<=6;$i++):?><option value="<?=$i?>" <?=(string)$i===$value?'selected':''?>>Kelas <?=$i?></option><?php endfor;?></select>
<?php elseif($type==='student'||$type==='teacher'):$options=module_options($pdo,$type);?><select name="<?=e($key)?>" id="<?=e($key)?>" <?=$required?'required':''?>><option value="">Pilih<?= $type==='student'?' siswa':' guru'?></option><?php foreach($options as $option):?><option value="<?=(int)$option['id']?>" <?=(string)$option['id']===$value?'selected':''?>><?=e($option['label'])?></option><?php endforeach;?></select>
<?php elseif($type==='file'):?><input type="file" name="<?=e($key)?>" id="<?=e($key)?>" accept="image/jpeg,image/png,image/webp"><small>JPG, PNG, WEBP · Maksimal 2 MB<?=!empty($editing[$key])?' · Foto tersimpan: '.e($editing[$key]):''?></small>
<?php else:?><input type="<?=$type==='number'?'number':e($type)?>" name="<?=e($key)?>" id="<?=e($key)?>" value="<?=e($value)?>" <?=$required?'required':''?> <?=$type==='number'?'step="any"':''?>><?php endif;?></div><?php endforeach;?></div>
<div class="form-submit"><?php if($editing):?><a class="btn soft" href="manage.php?module=<?=e($module)?>">Batal edit</a><?php endif;?><button class="btn primary">Simpan</button></div></form></div>
<div class="panel"><div class="panel-head"><div><h2>Daftar data</h2><p><?=count($rows)?> data terbaru (maksimal 500 baris ditampilkan).</p></div><button class="btn soft" onclick="window.print()">Cetak / Simpan PDF</button></div>
<div class="table-wrap"><table><thead><tr><?php foreach($fields as $field):?><th><?=e($field[0])?></th><?php endforeach;?><th>Aksi</th></tr></thead><tbody>
<?php if(!$rows):?><tr><td class="empty" colspan="<?=count($fields)+1?>">Belum ada data. Tambahkan data melalui formulir di atas.</td></tr><?php endif;?>
<?php foreach($rows as $row):?><tr><?php foreach($fields as $key=>$field):$value=module_value($row,$key);if(in_array($key,['student_id','teacher_id'],true)&&$value!==''){$lookup=$key==='student_id'?'students':'teachers';$label=$key==='student_id'?'nama_lengkap':'name';$find=$pdo->prepare("SELECT `$label` FROM `$lookup` WHERE id=?");$find->execute([$value]);$value=(string)($find->fetchColumn()?:'Data dihapus');}if($key==='nik'&&$value!=='')$value='••••••••••••'.substr($value,-4);?><td><?php if($field[1]==='file'&&$value!==''):?><img class="avatar" src="../uploads/dokumen/<?=e($value)?>" alt="Foto"><?php else:?><?=e($value?:'—')?><?php endif;?></td><?php endforeach;?><td class="actions"><a href="manage.php?module=<?=e($module)?>&edit=<?=(int)$row['id']?>">Edit</a><?php if(($_SESSION['admin_role']??'admin')==='admin'):?><form method="post" data-confirm="Hapus data ini?"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?=(int)$row['id']?>"><button class="link-danger">Hapus</button></form><?php endif;?></td></tr><?php endforeach;?>
</tbody></table></div></div>
<?php require __DIR__ . '/../footer.php'; ?>
