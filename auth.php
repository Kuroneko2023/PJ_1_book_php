<?php

// ถ้าไม่มีตั๋ว (ไม่มี user_id ใน Session)
if (!isset($_SESSION['user_id'])) {
    // ส่งกลับไปหน้า Login
    header("Location: login.php");
    exit();
}
?>