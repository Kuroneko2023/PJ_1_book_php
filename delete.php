<?php
session_start();
require_once 'auth.php'; // เรียกยามมาเฝ้า (คนทั่วไปห้ามลบ)
require_once 'db_connect.php';

// เช็คว่ามีการส่งค่า ID มาไหม
if (isset($_GET['id'])) {
    // 1. รับค่า ID และป้องกัน SQL Injection (สำคัญมาก!)
    $id = $conn->real_escape_string($_GET['id']);

    // 2. ดึงชื่อรูปภาพเก่ามาก่อน เพื่อเตรียมลบทิ้ง
    $sql_select = "SELECT image FROM books WHERE id = '$id'";
    $result = $conn->query($sql_select);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $image_to_delete = $row['image'];

        // 3. สั่งลบข้อมูลใน Database
        $sql_delete = "DELETE FROM books WHERE id = '$id'";

        if ($conn->query($sql_delete) === TRUE) {
            // 4. ถ้าลบใน DB สำเร็จ -> ให้ไปลบไฟล์รูปภาพออกจากโฟลเดอร์ uploads (ถ้ามี)
            if (!empty($image_to_delete)) {
                $file_path = "uploads/" . $image_to_delete;
                if (file_exists($file_path)) {
                    unlink($file_path); // คำสั่งลบไฟล์ออกจาก Server
                }
            }
            
            // แจ้งเตือนและเด้งกลับ
            echo "<script>alert('ลบข้อมูลเรียบร้อยแล้ว'); window.location='index.php';</script>";
        } else {
            // กรณีลบไม่ได้ (เช่น ติด Foreign Key)
            echo "<h1>❌ ลบไม่ได้!</h1>";
            echo "<h3>สาเหตุ: " . $conn->error . "</h3>";
            echo "<p>อาจเป็นเพราะหนังสือเล่มนี้ถูกอ้างอิงในตารางอื่น (เช่น ประวัติการขาย)</p>";
            echo "<a href='index.php'>กลับหน้าหลัก</a>";
        }
    } else {
        echo "<script>alert('ไม่พบข้อมูลหนังสือที่ต้องการลบ'); window.location='index.php';</script>";
    }

} else {
    // ถ้าไม่ได้ส่ง ID มา ให้กลับหน้าแรก
    header("location: index.php");
}
?>