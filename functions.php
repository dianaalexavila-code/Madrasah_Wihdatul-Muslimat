<?php
declare(strict_types=1);

function e(?string $value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
function require_login(): void {
    if (empty($_SESSION['admin_id'])) {
        header('Location: login.php');
        exit;
    }
}
function csrf_token(): string {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}
function verify_csrf(): void {
    $token = $_POST['csrf'] ?? '';
    if (!is_string($token) || !hash_equals($_SESSION['csrf'] ?? '', $token)) {
        http_response_code(419);
        exit('Sesi formulir tidak valid. Silakan kembali dan muat ulang halaman.');
    }
}
function age_from_date(?string $date): string {
    if (!$date) return '-';
    try {
        $birth = new DateTime($date);
        return (string)$birth->diff(new DateTime('today'))->y . ' tahun';
    } catch (Throwable $e) { return '-'; }
}
function redirect_with(string $message, string $type = 'success'): never {
    $_SESSION['flash'] = ['message' => $message, 'type' => $type];
    header('Location: siswa.php');
    exit;
}
function save_student_photo(array $file, ?string $old = null): ?string {
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) return $old;
    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Unggahan foto gagal.');
    }
    if (($file['size'] ?? 0) > MAX_UPLOAD_BYTES) {
        throw new RuntimeException('Ukuran foto maksimal 2 MB.');
    }
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    if (!isset($allowed[$mime])) throw new RuntimeException('Foto harus JPG, PNG, atau WEBP.');
    if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0750, true);
    $name = bin2hex(random_bytes(16)) . '.' . $allowed[$mime];
    if (!move_uploaded_file($file['tmp_name'], UPLOAD_DIR . $name)) {
        throw new RuntimeException('Tidak dapat menyimpan foto.');
    }
    if ($old && is_file(UPLOAD_DIR . basename($old))) @unlink(UPLOAD_DIR . basename($old));
    return $name;
}
