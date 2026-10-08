<?php
require __DIR__ . '/helpers.php';
require __DIR__ . '/db.php';
start_secure_session();
require_login();

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);

$stmt = $conn->prepare("SELECT * FROM pets WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

if (!$row) {
    die('ไม่พบข้อมูลที่ต้องการแก้ไข');
}
if ($row['owner_id'] != current_user_id() && !is_admin()) {
    die('คุณไม่มีสิทธิ์แก้ไขข้อมูลนี้');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $f1 = trim($_POST['pet_name'] ?? '');

    if ($f1 === '') {
        $error = 'กรุณากรอกชื่อสัตว์เลี้ยง';
    } else {
        $uploaded = safe_upload('photo');
        if ($uploaded === false) {
            $error = 'ไฟล์รูปไม่ถูกต้อง (รองรับเฉพาะ jpg, png, gif)';
        } else {
            $new_file = ($uploaded !== null) ? $uploaded : $row['photo'];

            $stmt = $conn->prepare(
                "UPDATE pets SET pet_name = ?, photo = ? WHERE id = ?"
            );
            $stmt->bind_param("ssi", $f1, $new_file, $id);

            $stmt->execute();
            header('Location: index.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>แก้ไขสัตว์เลี้ยง — ระบบจัดการสัตว์เลี้ยง (ฉบับฝึกหัด)</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="navbar">
  <div class="brand">ระบบจัดการสัตว์เลี้ยง (ฉบับฝึกหัด)</div>
  <div class="user-info">
    สวัสดี <b><?= e($_SESSION['full_name'] ?? '') ?></b>
    &nbsp;|&nbsp; <a href="logout.php">ออกจากระบบ</a>
  </div>
</div>
<div class="container">
  <div class="card">
    <h1>แก้ไขสัตว์เลี้ยง</h1>
    <?php if ($error): ?>
      <div class="alert alert-error"><?= e($error) ?></div>
    <?php endif; ?>
    <?php if (!empty($row['photo'])): ?>
      <p><img class="thumb" style="width:90px;height:90px" src="uploads/<?= e($row['photo']) ?>" alt=""></p>
    <?php endif; ?>
    <form method="post" enctype="multipart/form-data">
      <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
      <div class="form-group">
        <label>ชื่อสัตว์เลี้ยง</label>
        <input type="text" name="pet_name" value="<?= e($_POST['pet_name'] ?? $row['pet_name']) ?>" required>
      </div>
      <div class="form-group">
        <label>รูปสัตว์เลี้ยง (เลือกใหม่เฉพาะถ้าต้องการเปลี่ยน)</label>
        <input type="file" name="photo" accept="image/*">
      </div>
      <button class="btn" type="submit">บันทึกการแก้ไข</button>
      <a class="btn" style="background:#8a97a3" href="index.php">ยกเลิก</a>
    </form>
  </div>
</div>
</body>
</html>