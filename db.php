<?php
/**
 * db.php — เชื่อมต่อฐานข้อมูล TiDB Cloud
 */
$host   = 'gateway01.ap-southeast-1.prod.aws.tidbcloud.com';
$user   = '2ttpZavvinMybvY.root';
$pass   = 'mysql://2t1pZavvinMybvY.root:<PASSWORD>@gateway01.ap-southeast-1.prod.aws.tidbcloud.com:4000/test';
$dbname = 'test';
$port   = 4000;

$conn = mysqli_init();
$conn->ssl_set(NULL, NULL, NULL, NULL, NULL);
$conn->options(MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, false);

if (!$conn->real_connect($host, $user, $pass, $dbname, (int)$port, NULL, MYSQLI_CLIENT_SSL)) {
    die('เชื่อมต่อฐานข้อมูลไม่สำเร็จ: ' . mysqli_connect_error());
}

$conn->set_charset('utf8mb4');
