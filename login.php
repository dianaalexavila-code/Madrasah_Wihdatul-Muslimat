<?php
require __DIR__ . '/config.php';
require __DIR__ . '/functions.php';
if (!empty($_SESSION['admin_id'])) { header('Location: index.php'); exit; }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = strtolower(trim($_POST['email'] ?? ''));
    $stmt = $pdo->prepare('SELECT * FROM admins WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $admin = $stmt->fetch();
    if ($admin && password_verify($_POST['password'] ?? '', $admin['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_name'] = $admin['name'];
        $_SESSION['admin_role'] = $admin['role'] ?? 'admin';
        header('Location: index.php'); exit;
    }
    $error = 'Email atau kata sandi tidak sesuai.';
}
?>
<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Login Admin</title><link rel="stylesheet" href="style.css"></head><body class="login-bg"><main class="login-card"><div class="brand-mark">م</div><h1>Madrasah Diniyah</h1><p>Wihdatul Muslimat · Menteng, Jakarta Pusat</p><?php if($error):?><div class="alert error"><?=e($error)?></div><?php endif;?><form method="post"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><label>Email admin</label><input type="email" name="email" required autofocus><label>Kata sandi</label><input type="password" name="password" required><button class="btn primary full">Masuk ke Dashboard</button></form><small>Area khusus administrator madrasah.</small></main></body></html>
