<?php
/**
 * db.php — เชื่อมต่อฐานข้อมูล TiDB Cloud
 */
$host   = 'gateway01.ap-southeast-1.prod.aws.tidbcloud.com';
$user   = '2ttpZavvinMybvY.root';
$pass   = 'eLLk9KwH1uO58bgh';
$dbname = 'test';
$port   = 4000;

$conn = new mysqli($host, $user, $pass, $dbname, (int)$port);
$conn->set_charset('utf8mb4');

if ($conn->connect_error) {
    die('เชื่อมต่อฐานข้อมูลไม่สำเร็จ: ' . $conn->connect_error);
}
