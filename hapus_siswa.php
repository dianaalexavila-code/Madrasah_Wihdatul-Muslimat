<?php
require __DIR__ . '/config.php'; require __DIR__ . '/functions.php'; require_login();
if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);exit('Metode tidak diizinkan.');}
verify_csrf();$id=(int)($_POST['id']??0);
require_admin();
$stmt=$pdo->prepare('SELECT foto FROM students WHERE id=?');$stmt->execute([$id]);$row=$stmt->fetch();
if($row){$del=$pdo->prepare('DELETE FROM students WHERE id=?');$del->execute([$id]);if($row['foto']&&is_file(UPLOAD_DIR.basename($row['foto'])))@unlink(UPLOAD_DIR.basename($row['foto']));}
redirect_with('Data siswa telah dihapus.');
