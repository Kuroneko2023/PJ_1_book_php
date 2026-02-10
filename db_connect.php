<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pj_1_book"; 

require_once 'db_connect.php';
// สร้างการเชื่อมต่อ
$conn = new mysqli($servername, $username, $password, $dbname);

// ตั้งค่าให้รองรับภาษาไทย
$conn->set_charset("utf8mb4");

// เช็คว่าเชื่อมต่อได้ไหม
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// echo "Connected successfully"; // บรรทัดนี้คอมเมนต์ไว้ เวลาใช้จริงจะได้ไม่โชว์
?>