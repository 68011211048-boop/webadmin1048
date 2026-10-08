<?php
require __DIR__ . '/helpers.php';
require __DIR__ . '/db.php';
start_secure_session();

if (!empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username  = trim($_POST['username'] ?? '');
    $password  = $_POST['password'] ?? '';
    $full_name = trim($_POST['full_name'] ?? '');

    if ($username === '' || $password === '' || $full_name === '') {
        $error = 'กรุณากรอกข้อมูลให้ครบทุกช่อง';
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $error = 'ชื่อผู้ใช้นี้ถูกใช้งานแล้ว';
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $role = 'user';

            $stmt = $conn->prepare("INSERT INTO users (username, password_hash, full_name, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $username, $password_hash, $full_name, $role);
            if ($stmt->execute()) {
                header('Location: login.php?registered=1');
                exit;
            } else {
                $error = 'เกิดข้อผิดพลาดในการลงทะเบียน';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>สมัครสมาชิก — ระบบจัดการสัตว์เลี้ยง (ฉบับฝึกหัด)</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="login-wrap">
  <div class="card">
    <h1>สมัครสมาชิก</h1>
    <?php if ($error): ?>
      <div class="alert alert-error"><?= e($error) ?></div>
    <?php endif; ?>
    <form method="post">
      <div class="form-group">
        <label>ชื่อผู้ใช้ (Username)</label>
        <input type="text" name="username" value="<?= e($_POST['username'] ?? '') ?>" required autofocus>
      </div>
      <div class="form-group">
        <label>ชื่อ-นามสกุล</label>
        <input type="text" name="full_name" value="<?= e($_POST['full_name'] ?? '') ?>" required>
      </div>
      <div class="form-group">
        <label>รหัสผ่าน</label>
        <input type="password" name="password" required>
      </div>
      <button class="btn" type="submit" style="width:100%">สมัครสมาชิก</button>
    </form>
    <p class="hint-users">มีบัญชีอยู่แล้ว? <a href="login.php">เข้าสู่ระบบ</a></p>
  </div>
</div>
</body>
</html>