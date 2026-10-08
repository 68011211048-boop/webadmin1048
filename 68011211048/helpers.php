<?php
/**
 * helpers.php — ฟังก์ชันช่วยเหลือระบบ
 */
function e($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

function start_secure_session() {
    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
        session_start();
    }
}

function require_login() {
    if (empty($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}

function current_user_id() {
    return $_SESSION['user_id'] ?? null;
}

function is_admin() {
    return ($_SESSION['role'] ?? '') === 'admin';
}

function require_admin() {
    require_login();
    if (!is_admin()) {
        die('คุณไม่มีสิทธิ์เข้าถึงหน้านี้ (สำหรับผู้ดูแลระบบเท่านั้น)');
    }
}

function safe_upload($field_name, $dest_dir = null) {
    if ($dest_dir === null) $dest_dir = __DIR__ . '/uploads';
    if (empty($_FILES[$field_name]['name'])) return null;
    if ($_FILES[$field_name]['error'] !== UPLOAD_ERR_OK) return false;
    $tmp = $_FILES[$field_name]['tmp_name'];
    $info = @getimagesize($tmp);
    if ($info === false) return false;
    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif'];
    if (!isset($allowed[$info['mime']])) return false;
    $ext = $allowed[$info['mime']];
    $newname = bin2hex(random_bytes(8)) . '.' . $ext;
    if (!is_dir($dest_dir)) mkdir($dest_dir, 0755, true);
    if (!move_uploaded_file($tmp, $dest_dir . '/' . $newname)) return false;
    return $newname;
}