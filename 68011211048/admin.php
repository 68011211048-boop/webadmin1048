<?php
require __DIR__ . '/helpers.php';
require __DIR__ . '/db.php';
start_secure_session();
require_admin();

$msg = '';
$error = '';

// จัดการ Action (เปลี่ยนสิทธิ์ หรือ ลบผู้ใช้)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $target_user_id = (int)($_POST['user_id'] ?? 0);

    // ป้องกันไม่ให้ admin ทำการแก้ไข/ลบ บัญชีตัวเอง
    if ($target_user_id === (int)current_user_id()) {
        $error = 'คุณไม่สามารถเปลี่ยนสิทธิ์หรือลบบัญชีตัวเองได้';
    } else {
        if ($action === 'toggle_role') {
            $new_role = ($_POST['current_role'] === 'admin') ? 'user' : 'admin';
            $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
            $stmt->bind_param("si", $new_role, $target_user_id);
            $stmt->execute();
            $msg = 'อัปเดตสิทธิ์ผู้ใช้เรียบร้อยแล้ว';
        } elseif ($action === 'delete_user') {
            $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt->bind_param("i", $target_user_id);
            $stmt->execute();
            $msg = 'ลบผู้ใช้งานเรียบร้อยแล้ว';
        }
    }
}

// ดึงรายชื่อผู้ใช้ทั้งหมดพร้อมจำนวนสัตว์เลี้ยง
$sql = "SELECT users.*, COUNT(pets.id) AS pet_count 
        FROM users 
        LEFT JOIN pets ON users.id = pets.owner_id 
        GROUP BY users.id 
        ORDER BY users.id ASC";
$result = $conn->query($sql);
$users = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>จัดการผู้ใช้ — ระบบจัดการสัตว์เลี้ยง</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="navbar">
  <div class="brand">ระบบจัดการสัตว์เลี้ยง</div>
  <div class="user-info">
    สวัสดี <b><?= e($_SESSION['full_name'] ?? '') ?></b> (Admin)
    &nbsp;|&nbsp; <a href="index.php">กลับหน้าหลัก</a>
    &nbsp;|&nbsp; <a href="logout.php">ออกจากระบบ</a>
  </div>
</div>
<div class="container">
  <div class="card">
    <h1>จัดการผู้ใช้ (สำหรับ Admin)</h1>
    
    <?php if ($msg): ?>
      <div class="alert" style="background:#E9F7EF; color:var(--green); border:1px solid #A9DFBF;"><?= e($msg) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
      <div class="alert alert-error"><?= e($error) ?></div>
    <?php endif; ?>

    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>ชื่อผู้ใช้</th>
          <th>ชื่อ-นามสกุล</th>
          <th>บทบาท</th>
          <th>สัตว์เลี้ยง</th>
          <th>จัดการ</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($users as $u): ?>
        <tr>
          <td><?= (int)$u['id'] ?></td>
          <td><?= e($u['username']) ?></td>
          <td><?= e($u['full_name']) ?></td>
          <td>
            <?php if ($u['role'] === 'admin'): ?>
              <span class="owner-tag" style="background:#FDF0E4; color:var(--orange);">admin</span>
            <?php else: ?>
              <span class="muted">user</span>
            <?php endif; ?>
          </td>
          <td><?= (int)$u['pet_count'] ?> รายการ</td>
          <td>
            <?php if ($u['id'] != current_user_id()): ?>
              <!-- ปุ่มสลับสิทธิ์ admin / user -->
              <form method="post" style="display:inline-block; max-width:none;">
                <input type="hidden" name="action" value="toggle_role">
                <input type="hidden" name="user_id" value="<?= (int)$u['id'] ?>">
                <input type="hidden" name="current_role" value="<?= e($u['role']) ?>">
                <button type="submit" class="btn btn-sm">
                  <?= $u['role'] === 'admin' ? 'ปรับเป็น User' : 'ตั้งเป็น Admin' ?>
                </button>
              </form>

              <!-- ปุ่มลบผู้ใช้งาน -->
              <form method="post" style="display:inline-block; max-width:none;" onsubmit="return confirm('ยืนยันลบผู้ใช้งานนี้? (ข้อมูลสัตว์เลี้ยงทั้งหมดของผู้ใช้คนนี้จะถูกลบไปด้วย)');">
                <input type="hidden" name="action" value="delete_user">
                <input type="hidden" name="user_id" value="<?= (int)$u['id'] ?>">
                <button type="submit" class="btn btn-sm btn-danger">ลบ</button>
              </form>
            <?php else: ?>
              <span class="muted">(บัญชีปัจจุบัน)</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>