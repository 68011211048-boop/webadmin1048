<?php
require __DIR__ . '/helpers.php';
require __DIR__ . '/db.php';
start_secure_session();
require_login();

$id = (int)($_GET['id'] ?? 0);

$stmt = $conn->prepare("SELECT owner_id FROM pets WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

if (!$row) {
    die('ไม่พบข้อมูลที่ต้องการลบ');
}

if ($row['owner_id'] != current_user_id() && !is_admin()) {
    die('คุณไม่มีสิทธิ์ลบข้อมูลนี้');
}

if (is_admin()) {
    $stmt = $conn->prepare("DELETE FROM pets WHERE id = ?");
    $stmt->bind_param("i", $id);
} else {
    $owner_id = current_user_id();
    $stmt = $conn->prepare("DELETE FROM pets WHERE id = ? AND owner_id = ?");
    $stmt->bind_param("ii", $id, $owner_id);
}

$stmt->execute();
header('Location: index.php');
exit;