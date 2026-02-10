<?php
session_start();
require_once 'auth.php'; // เรียกยามมาเฝ้า
require_once 'db_connect.php';

if (!isset($_GET['id'])) { header("location: index.php"); exit(); }

// ป้องกัน SQL Injection ที่ตัวแปร ID
$id = $conn->real_escape_string($_GET['id']);

// 1. ดึงข้อมูลหนังสือเก่า
$sql = "SELECT * FROM books WHERE id = '$id'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

if (!$row) { 
    echo "ไม่พบข้อมูลหนังสือ"; 
    exit(); 
}

// 2. ดึงหมวดหมู่ทั้งหมดมาเตรียมไว้ (ตัด ORDER BY ออกกัน Error)
$cat_sql = "SELECT * FROM categories";
$cat_result = $conn->query($cat_sql);

// 3. บันทึกข้อมูลเมื่อกดปุ่ม
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // รับค่าและป้องกันอักขระพิเศษ
    $isbn = $conn->real_escape_string($_POST['isbn']);
    $title = $conn->real_escape_string($_POST['title']);
    $author = $conn->real_escape_string($_POST['author']);
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $category_id = $_POST['category_id'];
    $old_image = $_POST['old_image'];
    $image_file = $old_image;

    // ส่วนจัดการอัปโหลดรูปภาพใหม่
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . time() . "_" . basename($_FILES["image"]["name"]);
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_file = basename($target_file);
            // ลบรูปเก่าทิ้ง (ถ้ามี)
            if (!empty($old_image) && file_exists("uploads/".$old_image)) { 
                unlink("uploads/".$old_image); 
            }
        }
    }

    // SQL Update ข้อมูล
    $sql_update = "UPDATE books SET 
                   isbn='$isbn', title='$title', author='$author', 
                   price='$price', stock='$stock', image='$image_file',
                   category_id='$category_id' 
                   WHERE id='$id'";

    if ($conn->query($sql_update) === TRUE) {
        echo "<script>alert('แก้ไขข้อมูลสำเร็จ!'); window.location='index.php';</script>";
    } else {
        echo "<h1>❌ เกิดข้อผิดพลาด:</h1>";
        echo "Error: " . $conn->error;
        echo "<br><a href='edit.php?id=$id'>ลองใหม่</a>";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แก้ไขข้อมูลหนังสือ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <h4>แก้ไขข้อมูลหนังสือ</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="" enctype="multipart/form-data">
                            
                            <div class="mb-3 text-center">
                                <label class="d-block mb-2">รูปภาพปัจจุบัน</label>
                                <?php if(!empty($row['image'])): ?>
                                    <img src="uploads/<?php echo $row['image']; ?>" width="150" class="img-thumbnail">
                                <?php else: ?>
                                    <p class="text-muted">ไม่มีรูปภาพ</p>
                                <?php endif; ?>
                                <input type="hidden" name="old_image" value="<?php echo $row['image']; ?>">
                            </div>

                            <div class="mb-3">
                                <label>เปลี่ยนรูปภาพ</label>
                                <input type="file" name="image" class="form-control" accept="image/*">
                            </div>

                            <div class="mb-3">
                                <label>หมวดหมู่ / ซีรีส์</label>
                                <select name="category_id" class="form-select" required>
                                    <option value="">-- เลือกหมวดหมู่ --</option>
                                    <?php 
                                    if ($cat_result->num_rows > 0) {
                                        $cat_result->data_seek(0);
                                        while($cat = $cat_result->fetch_assoc()) {
                                            $selected = ($cat['id'] == $row['category_id']) ? "selected" : "";
                                            
                                            // หาชื่อคอลัมน์อัตโนมัติ
                                            $cat_name = "ไม่ระบุชื่อ";
                                            if (isset($cat['name'])) $cat_name = $cat['name'];
                                            elseif (isset($cat['NAME'])) $cat_name = $cat['NAME'];
                                            elseif (isset($cat['category_name'])) $cat_name = $cat['category_name'];
                                            
                                            echo '<option value="'.$cat['id'].'" '.$selected.'>'.$cat_name.'</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label>รหัสหนังสือ (ISBN)</label>
                                <input type="text" name="isbn" class="form-control" value="<?php echo $row['isbn']; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label>ชื่อหนังสือ</label>
                                <input type="text" name="title" class="form-control" value="<?php echo $row['title']; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label>ผู้แต่ง</label>
                                <input type="text" name="author" class="form-control" value="<?php echo $row['author']; ?>">
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>ราคา</label>
                                    <input type="number" step="0.01" name="price" class="form-control" value="<?php echo $row['price']; ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>จำนวนสต็อก</label>
                                    <input type="number" name="stock" class="form-control" value="<?php echo $row['stock']; ?>" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-warning w-100">บันทึกการแก้ไข</button>
                            <a href="index.php" class="btn btn-secondary w-100 mt-2">ยกเลิก</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>