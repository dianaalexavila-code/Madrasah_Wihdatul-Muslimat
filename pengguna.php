<?php
require __DIR__.'/../config.php'; require __DIR__.'/../functions.php'; require_login(); require_admin();
$pageTitle='Pengguna Sistem'; $error=''; $flash=$_SESSION['flash']??null; unset($_SESSION['flash']);
if($_SERVER['REQUEST_METHOD']==='POST'){
    verify_csrf();
    try {
        if(($_POST['action']??'')==='delete'){
            $id=(int)($_POST['id']??0);
            if($id===(int)$_SESSION['admin_id']) throw new RuntimeException('Akun yang sedang digunakan tidak dapat dihapus.');
            $count=(int)$pdo->query("SELECT COUNT(*) FROM admins WHERE role='admin'")->fetchColumn();
            $target=$pdo->prepare('SELECT role FROM admins WHERE id=?'); $target->execute([$id]); $role=$target->fetchColumn();
            if($role==='admin'&&$count<=1) throw new RuntimeException('Administrator terakhir tidak dapat dihapus.');
            $delete=$pdo->prepare('DELETE FROM admins WHERE id=?'); $delete->execute([$id]);
            $_SESSION['flash']=['message'=>'Pengguna berhasil dihapus.','type'=>'success'];
            header('Location: pengguna.php'); exit;
        }
        $name=trim((string)($_POST['name']??'')); $email=strtolower(trim((string)($_POST['email']??'')));
        $password=(string)($_POST['password']??''); $role=(string)($_POST['role']??'operator');
        if($name===''||!filter_var($email,FILTER_VALIDATE_EMAIL)||strlen($password)<12||!in_array($role,['admin','operator'],true)) throw new RuntimeException('Isi nama, email valid, peran, dan kata sandi minimal 12 karakter.');
        $insert=$pdo->prepare('INSERT INTO admins(name,email,password_hash,role) VALUES(?,?,?,?)');
        $insert->execute([$name,$email,password_hash($password,PASSWORD_DEFAULT),$role]);
        $_SESSION['flash']=['message'=>'Pengguna berhasil dibuat.','type'=>'success'];
        header('Location: pengguna.php'); exit;
    } catch(Throwable $exception) {
        $error=$exception instanceof PDOException?'Email mungkin sudah digunakan atau operasi database gagal.':$exception->getMessage();
    }
}
$users=$pdo->query('SELECT id,name,email,role,created_at FROM admins ORDER BY id')->fetchAll();
require __DIR__.'/../header.php';
?>
<div class="page-actions"><div><h1>Pengguna Sistem</h1><p>Administrator mengelola akun. Operator dapat menginput dan memperbarui data, tetapi tidak menghapus data.</p></div></div>
<?php if($flash):?><div class="alert <?=e($flash['type'])?>"><?=e($flash['message'])?></div><?php endif;?><?php if($error):?><div class="alert error"><?=e($error)?></div><?php endif;?>
<div class="panel form-panel"><h2>Tambah akun pengguna</h2><form method="post"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><div class="form-grid">
<div class="field"><label>Nama</label><input name="name" required></div><div class="field"><label>Email</label><input type="email" name="email" required></div>
<div class="field"><label>Kata sandi (minimal 12 karakter)</label><input type="password" name="password" minlength="12" required></div>
<div class="field"><label>Peran</label><select name="role"><option value="operator">Operator</option><option value="admin">Administrator</option></select></div></div><div class="form-submit"><button class="btn primary">Buat akun</button></div></form></div>
<div class="panel"><div class="table-wrap"><table><thead><tr><th>Nama</th><th>Email</th><th>Peran</th><th>Dibuat</th><th>Aksi</th></tr></thead><tbody><?php foreach($users as $user):?><tr><td><?=e($user['name'])?></td><td><?=e($user['email'])?></td><td><?=e(ucfirst($user['role']))?></td><td><?=e($user['created_at'])?></td><td><?php if((int)$user['id']!==(int)$_SESSION['admin_id']):?><form method="post" data-confirm="Hapus akun ini?"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?=(int)$user['id']?>"><button class="link-danger">Hapus</button></form><?php else:?>Akun aktif<?php endif;?></td></tr><?php endforeach;?></tbody></table></div></div>
<?php require __DIR__.'/../footer.php'; ?>
