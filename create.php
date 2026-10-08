<?php
require __DIR__ . '/helpers.php';
require __DIR__ . '/db.php';
start_secure_session();
require_login();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $f1 = trim($_POST['pet_name'] ?? '');

    if ($f1 === '') {
        $error = 'กรุณากรอกชื่อสัตว์เลี้ยง';
    } else {
        $filename = safe_upload('photo');

        if ($filename === false) {
            $error = 'ไฟล์รูปไม่ถูกต้อง (รองรับเฉพาะ jpg, png, gif)';
        } else {
            $owner_id = current_user_id();
            $stmt = $conn->prepare(
                "INSERT INTO pets (pet_name, photo, owner_id) VALUES (?, ?, ?)"
            );
            $stmt->bind_param("ssi", $f1, $filename, $owner_id);

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
<title>เพิ่มสัตว์เลี้ยง — ระบบจัดการสัตว์เลี้ยง (ฉบับฝึกหัด)</title>
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
    <h1>เพิ่มสัตว์เลี้ยงใหม่</h1>
    <?php if ($error): ?>
      <div class="alert alert-error"><?= e($error) ?></div>
    <?php endif; ?>
    <form method="post" enctype="multipart/form-data">
      <div class="form-group">
        <label>ชื่อสัตว์เลี้ยง</label>
        <input type="text" name="pet_name" value="<?= e($_POST['pet_name'] ?? '') ?>" required>
      </div>
      <div class="form-group">
        <label>รูปสัตว์เลี้ยง</label>
        <input type="file" name="photo" accept="image/*">
      </div>
      <button class="btn" type="submit">บันทึก</button>
      <a class="btn" style="background:#8a97a3" href="index.php">ยกเลิก</a>
    </form>
  </div>
</div>
</body>
</html>