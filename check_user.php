<?php
require_once 'db_connect.php';

// ดึงข้อมูล User มาดูสัก 1 คน เพื่อดูชื่อคอลัมน์
$sql = "SELECT * FROM users LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    
    echo "<div style='background: yellow; padding: 20px; font-size: 20px;'>";
    echo "<b>รายชื่อคอลัมน์ในตาราง users คือ:</b><br><pre>";
    
    // สั่งปริ้นชื่อคอลัมน์ทั้งหมดออกมาดู
    print_r(array_keys($row)); 
    
    echo "</pre></div>";
} else {
    echo "ยังไม่มีข้อมูลในตาราง users เลย (คุณรัน setup_admin.php หรือยังครับ?)";
}
?>