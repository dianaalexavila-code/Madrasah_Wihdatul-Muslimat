<?php
require __DIR__.'/../config.php'; require __DIR__.'/../functions.php'; require_login();
$pageTitle='Laporan & Ekspor';
$attendanceSummary=$pdo->query("SELECT attendance_date,status,COUNT(*) total FROM attendance GROUP BY attendance_date,status ORDER BY attendance_date DESC LIMIT 50")->fetchAll();
if (isset($_GET['export']) && $_GET['export']==='siswa') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="data-siswa-' . date('Y-m-d') . '.csv"');
    $out=fopen('php://output','w'); fwrite($out,"\xEF\xBB\xBF");
    fputcsv($out,['Nomor Induk','NISN','Nama Lengkap','Kelas','Jenis Kelamin','Tahun Masuk','Status','Kontak Wali']);
    $stmt=$pdo->query("SELECT nomor_induk,nisn,nama_lengkap,kelas,jenis_kelamin,tahun_masuk,status,COALESCE(NULLIF(telepon_wali,''),NULLIF(telepon_ayah,''),telepon_ibu) kontak FROM students ORDER BY kelas,nama_lengkap");
    while($row=$stmt->fetch(PDO::FETCH_NUM)) fputcsv($out,$row);
    fclose($out); exit;
}
if (isset($_GET['backup'])) {
    require_admin();
    header('Content-Type: application/sql; charset=utf-8');
    header('Content-Disposition: attachment; filename="backup-madrasah-' . date('Y-m-d-His') . '.sql"');
    echo "-- Backup data aplikasi, dibuat " . date(DATE_ATOM) . "\n";
    echo file_get_contents(__DIR__.'/../database.sql') . "\nSET FOREIGN_KEY_CHECKS=0;\n";
    foreach(['admins','teachers','students','classes','attendance','grades','promotions','events','calendar_events','facilities','organization'] as $table){
        $rows=$pdo->query("SELECT * FROM `$table`");
        while($row=$rows->fetch(PDO::FETCH_ASSOC)){
            $columns=array_map(static fn($column)=>'`'.str_replace('`','``',$column).'`',array_keys($row));
            $values=array_map(static fn($value)=>$value===null?'NULL':$GLOBALS['pdo']->quote((string)$value),array_values($row));
            echo 'INSERT INTO `'.$table.'` ('.implode(',',$columns).') VALUES ('.implode(',',$values).");\n";
        }
    }
    echo "SET FOREIGN_KEY_CHECKS=1;\n"; exit;
}
require __DIR__.'/../header.php';
?>
<div class="page-actions"><div><h1>Laporan & Ekspor</h1><p>Ekspor data operasional dan cetak laporan menggunakan fitur cetak browser.</p></div></div>
<div class="stats"><article class="stat"><span>Data siswa</span><strong><?=(int)$pdo->query('SELECT COUNT(*) FROM students')->fetchColumn()?></strong><small>Rekap induk siswa</small></article><article class="stat"><span>Data guru</span><strong><?=(int)$pdo->query('SELECT COUNT(*) FROM teachers')->fetchColumn()?></strong><small>Guru dan pengajar</small></article><article class="stat"><span>Absensi tercatat</span><strong><?=(int)$pdo->query('SELECT COUNT(*) FROM attendance')->fetchColumn()?></strong><small>Semua tanggal</small></article></div>
<div class="panel"><h2>Ekspor daftar siswa</h2><p>Format CSV UTF-8 dapat dibuka dan diolah di Microsoft Excel atau aplikasi spreadsheet.</p><div class="form-submit"><a class="btn primary" href="laporan.php?export=siswa">Unduh Excel (CSV)</a><button class="btn soft" onclick="window.print()">Cetak / Simpan PDF</button></div><p>NIK dan data identitas sensitif tidak dimasukkan ke ekspor.</p></div>
<div class="panel"><h2>Rekap kehadiran</h2><p>Rekap per tanggal dan status kehadiran yang sudah tercatat.</p><div class="table-wrap"><table><thead><tr><th>Tanggal</th><th>Status</th><th>Jumlah</th></tr></thead><tbody><?php if(!$attendanceSummary):?><tr><td class="empty" colspan="3">Belum ada data absensi.</td></tr><?php endif;?><?php foreach($attendanceSummary as $item):?><tr><td><?=e(date('d-m-Y',strtotime($item['attendance_date'])))?></td><td><?=e($item['status'])?></td><td><?=e((string)$item['total'])?></td></tr><?php endforeach;?></tbody></table></div></div>
<?php if(($_SESSION['admin_role']??'')==='admin'):?><div class="panel"><h2>Backup database</h2><p>Unduh dump SQL dari skema dan seluruh tabel database. File berisi data pribadi; simpan di lokasi terenkripsi dengan akses terbatas. Foto perlu disalin terpisah dari folder uploads.</p><a class="btn primary" href="laporan.php?backup=1">Unduh backup SQL</a></div><?php endif;?>
<?php require __DIR__.'/../footer.php'; ?>
