<?php
/**
 * db.php — เชื่อมต่อฐานข้อมูล
 */
$host   = 'localhost';
$user   = 'root';
$pass   = '';
$dbname = 'pet_lab_db';

$conn = new mysqli($host, $user, $pass, $dbname);
$conn->set_charset('utf8mb4');
if ($conn->connect_error) {
    die('เชื่อมต่อฐานข้อมูลไม่สำเร็จ: ' . $conn->connect_error .
        ' (ตรวจสอบว่าได้ import pet_lab_db.sql และเปิด MySQL แล้ว)');
}