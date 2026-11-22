<?php
require __DIR__ . '/config.php';
require __DIR__ . '/functions.php';
$done = false;
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = trim($_POST['name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 12) {
        $error = 'Isi nama, email yang valid, dan kata sandi minimal 12 karakter.';
    } else {
        try {
            $pdo->exec("CREATE TABLE IF NOT EXISTS admins (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(120) NOT NULL,
                email VARCHAR(190) NOT NULL UNIQUE,
                password_hash VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
            $pdo->exec("CREATE TABLE IF NOT EXISTS students (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                nomor_induk VARCHAR(40) NOT NULL UNIQUE,
                nisn VARCHAR(20) NULL,
                nama_lengkap VARCHAR(160) NOT NULL,
                nik VARCHAR(16) NULL,
                tempat_lahir VARCHAR(100) NULL,
                tanggal_lahir DATE NULL,
                jenis_kelamin ENUM('Laki-laki','Perempuan') NOT NULL,
                alamat TEXT NULL,
                rt_rw VARCHAR(30) NULL,
                kelurahan VARCHAR(100) NULL,
                kecamatan VARCHAR(100) NULL,
                kota VARCHAR(100) NULL,
                kelas TINYINT UNSIGNED NOT NULL,
                tahun_masuk YEAR NULL,
                nama_ayah VARCHAR(160) NULL,
                nik_ayah VARCHAR(16) NULL,
                pekerjaan_ayah VARCHAR(120) NULL,
                telepon_ayah VARCHAR(25) NULL,
                nama_ibu VARCHAR(160) NULL,
                nik_ibu VARCHAR(16) NULL,
                pekerjaan_ibu VARCHAR(120) NULL,
                telepon_ibu VARCHAR(25) NULL,
                nama_wali VARCHAR(160) NULL,
                hubungan_wali VARCHAR(80) NULL,
                telepon_wali VARCHAR(25) NULL,
                alamat_wali TEXT NULL,
                foto VARCHAR(255) NULL,
                status ENUM('Aktif','Lulus','Pindah','Tidak Aktif') NOT NULL DEFAULT 'Aktif',
                catatan TEXT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_nama (nama_lengkap),
                INDEX idx_kelas (kelas),
                INDEX idx_status (status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
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
<?php if ($done): ?><div class="alert success">Tabel database siap dan akun admin telah tersedia. Hapus atau ubah nama file install.php setelah instalasi, lalu masuk melalui login.php.</div><a class="btn primary full" href="login.php">Ke halaman login</a>
<?php else: ?><?php if ($error): ?><div class="alert error"><?=e($error)?></div><?php endif; ?>
<form method="post"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><label>Nama administrator</label><input name="name" required><label>Email admin</label><input type="email" name="email" required><label>Kata sandi (minimal 12 karakter)</label><input type="password" name="password" minlength="12" required><button class="btn primary full">Buat tabel dan akun admin</button></form><?php endif; ?>
</main></body></html>
