<?php
require __DIR__ . '/helpers.php';
require __DIR__ . '/db.php';
start_secure_session();

if (!empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$msg = '';
if (isset($_GET['registered'])) {
    $msg = 'สมัครสมาชิกสำเร็จ! กรุณาเข้าสู่ระบบ';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT id, username, password_hash, full_name, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user && password_verify($password, $user['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['role']      = $user['role'];
        header('Location: index.php');
        exit;
    } else {
        $error = 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง';
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>เข้าสู่ระบบ — ระบบจัดการสัตว์เลี้ยง (ฉบับฝึกหัด)</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="login-wrap">
  <div class="card">
    <h1>ระบบจัดการสัตว์เลี้ยง</h1>
    <?php if ($msg): ?>
      <div class="alert" style="background:#E9F7EF; color:var(--green); border:1px solid #A9DFBF;"><?= e($msg) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
      <div class="alert alert-error"><?= e($error) ?></div>
    <?php endif; ?>
    <form method="post">
      <div class="form-group">
        <label>ชื่อผู้ใช้</label>
        <input type="text" name="username" value="<?= e($_POST['username'] ?? '') ?>" required autofocus>
      </div>
      <div class="form-group">
        <label>รหัสผ่าน</label>
        <input type="password" name="password" required>
      </div>
      <button class="btn" type="submit" style="width:100%">เข้าสู่ระบบ</button>
    </form>
    <p class="hint-users">ยังไม่มีบัญชี? <a href="register.php">สมัครสมาชิก</a></p>
    <p class="hint-users">ทดสอบด้วย: <b>admin</b>, <b>kanya</b> หรือ <b>chai</b> &nbsp;·&nbsp; รหัสผ่าน <b>1234</b></p>
  </div>
</div>
</body>
</html>