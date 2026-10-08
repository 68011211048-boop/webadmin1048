<?php
/**
 * db.php — เชื่อมต่อฐานข้อมูล
 */
$host   = getenv('DB_HOST') ?: 'localhost';
$user   = getenv('DB_USER') ?: 'root';
$pass   = getenv('DB_PASS') ?: '';
$dbname = getenv('DB_NAME') ?: 'pet_lab_db';
$port   = getenv('DB_PORT') ?: 3306;

$conn = new mysqli($host, $user, $pass, $dbname, (int)$port);
$conn->set_charset('utf8mb4');

if ($conn->connect_error) {
    die('เชื่อมต่อฐานข้อมูลไม่สำเร็จ: ' . $conn->connect_error);
}
