<?php
declare(strict_types=1);
/*
 * Isi konfigurasi database hosting/XAMPP di sini.
 * Jangan unggah file ini ke repositori publik.
 */
define('DB_HOST', 'localhost');
define('DB_NAME', 'db_madrasah_wm');
define('DB_USER', 'root');
define('DB_PASS', '');
define('APP_NAME', 'Madrasah Diniyah Wihdatul Muslimat');
define('UPLOAD_DIR', __DIR__ . '/uploads/foto_siswa/');
define('MAX_UPLOAD_BYTES', 2 * 1024 * 1024);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    exit('Koneksi database gagal. Periksa konfigurasi pada config.php.');
}
