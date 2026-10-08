<?php
require __DIR__ . '/helpers.php';
require __DIR__ . '/db.php';
start_secure_session();
require_login();

$sql = "SELECT pets.*, users.full_name FROM pets
        JOIN users ON pets.owner_id = users.id
        ORDER BY pets.id DESC";
$result = $conn->query($sql);
$rows = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>ระบบจัดการสัตว์เลี้ยง</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="navbar">
  <div class="brand">ระบบจัดการสัตว์เลี้ยง</div>
  <div class="user-info">
    สวัสดี <b><?= e($_SESSION['full_name'] ?? '') ?></b>
    <?php if (is_admin()): ?>
      &nbsp;|&nbsp; <a href="admin.php" style="color:#FFB07A; font-weight:600;">[ หน้าผู้ดูแลระบบ ]</a>
    <?php endif; ?>
    &nbsp;|&nbsp; <a href="logout.php">ออกจากระบบ</a>
  </div>
</div>
<div class="container">
  <div class="card">
    <h1>รายการสัตว์เลี้ยงทั้งหมด</h1>
    <a class="btn btn-add" href="create.php">+ เพิ่มสัตว์เลี้ยง</a>

    <?php if (empty($rows)): ?>
      <p class="empty">ยังไม่มีข้อมูล</p>
    <?php else: ?>
    <table>
      <thead>
        <tr><th>รูปสัตว์เลี้ยง</th><th>ชื่อสัตว์เลี้ยง</th><th>เพิ่มโดย</th><th>จัดการ</th></tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
        <tr>
          <td>
            <?php if (!empty($r['photo'])): ?>
              <img class="thumb" src="uploads/<?= e($r['photo']) ?>" alt="">
            <?php else: ?>
              <span class="muted">ไม่มีรูป</span>
            <?php endif; ?>
          </td>
          <td><?= e($r['pet_name']) ?></td>
          <td>
            <?= e($r['full_name']) ?>
            <?php if ($r['owner_id'] == current_user_id()): ?>
              <span class="owner-tag">ของคุณ</span>
            <?php endif; ?>
          </td>
          <td>
            <?php if ($r['owner_id'] == current_user_id() || is_admin()): ?>
              <a class="btn btn-sm" href="edit.php?id=<?= (int)$r['id'] ?>">แก้ไข</a>
              <a class="btn btn-sm btn-danger" href="delete.php?id=<?= (int)$r['id'] ?>"
                 onclick="return confirm('ยืนยันลบสัตว์เลี้ยงนี้?')">ลบ</a>
            <?php else: ?>
              <span class="muted">—</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php endif; ?>
  </div>
</div>
</body>
</html>