<?php
require_once 'db_connect.php';

// กำหนด Username และ Password ที่ต้องการ
$username = 'admin';
$password = '1234'; 

// แปลงรหัสผ่านเป็น Hash (ปลอดภัยสูง)
$password_hash = password_hash($password, PASSWORD_DEFAULT);

// ลบคนเก่าออกก่อน (ถ้ามี) แล้วเพิ่มคนใหม่เข้าไป
$conn->query("DELETE FROM users WHERE username = '$username'");
$sql = "INSERT INTO users (username, password, role) VALUES ('$username', '$password_hash', 'admin')";

if ($conn->query($sql) === TRUE) {
    echo "<h1>✅ สร้าง User Admin สำเร็จ!</h1>";
    echo "Username: <b>admin</b><br>";
    echo "Password: <b>1234</b><br>";
    echo "<br>รหัสผ่านใน Database ถูกเก็บเป็น: " . $password_hash;
    echo "<br><br><a href='login.php'>ไปหน้า Login ได้เลย</a>";
} else {
    echo "Error: " . $conn->error;
}
?>