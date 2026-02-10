<?php
session_start();
require_once 'auth.php'; // เรียกยามมาเฝ้า (ต้องล็อกอินก่อน)
require_once 'db_connect.php';

// --- 1. ดึงหมวดหมู่ (ตัด ORDER BY เพื่อกัน Error) ---
$cat_sql = "SELECT * FROM categories"; 
$cat_result = $conn->query($cat_sql);

// --- 2. ส่วนบันทึกข้อมูล (ทำงานเมื่อกดปุ่ม Submit) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // รับค่าและป้องกัน SQL Injection
    $isbn = $conn->real_escape_string($_POST['isbn']);
    $title = $conn->real_escape_string($_POST['title']);
    $author = $conn->real_escape_string($_POST['author']);
    $price = $_POST['price']; 
    $stock = $_POST['stock'];
    $category_id = $_POST['category_id'];
    
    // จัดการรูปภาพ
    $image_file = "";
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . time() . "_" . basename($_FILES["image"]["name"]);
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_file = basename($target_file); 
        }
    }

    // บันทึกข้อมูล
    $sql = "INSERT INTO books (isbn, title, author, price, stock, image, category_id) 
            VALUES ('$isbn', '$title', '$author', '$price', '$stock', '$image_file', '$category_id')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('เพิ่มข้อมูลสำเร็จ!'); window.location='index.php';</script>";
    } else {
        echo "<h1>❌ เกิดข้อผิดพลาด:</h1>";
        echo "Error: " . $conn->error;
        echo "<br><br><a href='add.php'>ลองใหม่</a>";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>เพิ่มหนังสือใหม่</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4>เพิ่มหนังสือใหม่</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="" enctype="multipart/form-data">
                            
                            <div class="mb-3">
                                <label>หมวดหมู่ / ซีรีส์</label>
                                <select name="category_id" class="form-select" required>
                                    <option value="">-- เลือกหมวดหมู่ --</option>
                                    <?php 
                                    if ($cat_result->num_rows > 0) {
                                        while($cat = $cat_result->fetch_assoc()) {
                                            // เช็คชื่อคอลัมน์ให้อัตโนมัติ (กัน Error ชื่อไม่ตรง)
                                            $cat_name = "ไม่ระบุชื่อ";
                                            if (isset($cat['name'])) $cat_name = $cat['name'];
                                            elseif (isset($cat['NAME'])) $cat_name = $cat['NAME'];
                                            elseif (isset($cat['category_name'])) $cat_name = $cat['category_name'];
                                            
                                            echo '<option value="'.$cat['id'].'">'.$cat_name.'</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label>รูปปกหนังสือ</label>
                                <input type="file" name="image" class="form-control" accept="image/*">
                            </div>
                            <div class="mb-3">
                                <label>รหัสหนังสือ (ISBN)</label>
                                <input type="text" name="isbn" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>ชื่อหนังสือ</label>
                                <input type="text" name="title" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>ผู้แต่ง</label>
                                <input type="text" name="author" class="form-control">
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>ราคา</label>
                                    <input type="number" step="0.01" name="price" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>จำนวนสต็อก</label>
                                    <input type="number" name="stock" class="form-control" required>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-success w-100">บันทึกข้อมูล</button>
                            <a href="index.php" class="btn btn-secondary w-100 mt-2">ย้อนกลับ</a>
                            
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>