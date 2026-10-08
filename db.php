<?php
/**
 * db.php — เชื่อมต่อ TiDB Cloud (ระบุ System CA Path เพื่อผ่าน TLS Handshake)
 */
$host   = 'gateway01.ap-southeast-1.prod.aws.tidbcloud.com';
$user   = '2ttpZavvinMybvY.root';
$pass   = '6vvGIXWF18vPWC77'; // ใส่รหัสผ่านล่าสุดที่คุณคัดลอกไว้ได้เลย
$dbname = 'test';
$port   = 4000;

// พาธ CA Certificate มาตรฐานของระบบ Linux ใน Docker (Render)
$ca_path = '/etc/ssl/certs/ca-certificates.crt';

$conn = mysqli_init();

if (file_exists($ca_path)) {
    $conn->ssl_set(NULL, NULL, $ca_path, NULL, NULL);
} else {
    $conn->ssl_set(NULL, NULL, NULL, NULL, NULL);
}

$conn->options(MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, false);

if (!$conn->real_connect($host, $user, $pass, $dbname, (int)$port, NULL, MYSQLI_CLIENT_SSL)) {
    die('เชื่อมต่อฐานข้อมูลไม่สำเร็จ: ' . mysqli_connect_error());
}

$conn->set_charset('utf8mb4');
