<?php
require __DIR__ . '/config.php';
require __DIR__ . '/functions.php';
$done = false;
$error = '';
try {
    $schema = file_get_contents(__DIR__ . '/database.sql');
    if ($schema === false) throw new RuntimeException('File database.sql tidak dapat dibaca.');
    foreach (preg_split('/;\s*(?:\r?\n|$)/', $schema) as $statement) {
        $statement = trim($statement);
        if ($statement === '' || stripos($statement, 'CREATE DATABASE') === 0 || stripos($statement, 'USE ') === 0) continue;
        $pdo->exec($statement);
    }
    $roleColumn = $pdo->query("SHOW COLUMNS FROM admins LIKE 'role'")->fetch();
    if (!$roleColumn) $pdo->exec("ALTER TABLE admins ADD COLUMN role ENUM('admin','operator') NOT NULL DEFAULT 'admin'");
} catch (Throwable $e) {
    $error = 'Penyiapan skema gagal. Periksa izin database dan file database.sql.';
}
try { $alreadyInstalled = (int)$pdo->query('SELECT COUNT(*) FROM admins')->fetchColumn() > 0; }
catch (PDOException $e) { $alreadyInstalled = false; }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = trim($_POST['name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    if ($alreadyInstalled) {
        $error = 'Instalasi sudah selesai. Gunakan halaman login.';
    } elseif ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 12) {
        $error = 'Isi nama, email yang valid, dan kata sandi minimal 12 karakter.';
    } else {
        try {
            $check = $pdo->prepare('SELECT id FROM admins WHERE email = ?');
            $check->execute([$email]);
            if (!$check->fetch()) {
                $stmt = $pdo->prepare('INSERT INTO admins(name,email,password_hash) VALUES(?,?,?)');
                $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);
            }
            $done = true;
        } catch (Throwable $e) {
            $error = 'Instalasi gagal. Periksa database dan konfigurasi.';
        }
    }
}
?>
<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Instalasi Madrasah</title><link rel="stylesheet" href="style.css"></head>
<body class="login-bg"><main class="login-card"><div class="brand-mark">م</div><h1>Instalasi Admin</h1><p>Madrasah Diniyah Wihdatul Muslimat</p>
<?php if ($error): ?><div class="alert error"><?=e($error)?></div><?php elseif ($done || $alreadyInstalled): ?><div class="alert success"><?=$done?'Tabel database siap dan akun admin telah tersedia.':'Instalasi sudah dilakukan. Skema telah diperiksa/diperbarui dan pembuatan akun baru melalui installer dinonaktifkan.'?> Hapus atau ubah nama file install.php setelah instalasi.</div><a class="btn primary full" href="login.php">Ke halaman login</a>
<?php else: ?>
<form method="post"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><label>Nama administrator</label><input name="name" required><label>Email admin</label><input type="email" name="email" required><label>Kata sandi (minimal 12 karakter)</label><input type="password" name="password" minlength="12" required><button class="btn primary full">Buat tabel dan akun admin</button></form><?php endif; ?>
</main></body></html>
