<?php
session_start();
session_destroy(); // ล้างข้อมูล Session ทั้งหมด (เหมือนฉีกตั๋วทิ้ง)
header("Location: login.php"); // ส่งกลับไปหน้า Login
exit();
?>