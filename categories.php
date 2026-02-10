<?php
require_once 'auth.php';
require_once 'db_connect.php';

// --- ส่วนที่ 1: บันทึกข้อมูล ---
if (isset($_POST['save_category'])) {
    // รับค่าจากฟอร์ม
    $name = $_POST['name'];
    
    if (!empty($name)) {
        $name = $conn->real_escape_string($name);
        
        // ใช้ชื่อคอลัมน์ NAME (ตัวใหญ่) ตามที่ Database ต้องการ
        $sql = "INSERT INTO categories (NAME) VALUES ('$name')";
        
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('เพิ่มหมวดหมู่สำเร็จ!'); window.location='categories.php';</script>";
        } else {
            echo "Error: " . $conn->error;
        }
    }
}

// --- ส่วนที่ 2: ลบข้อมูล ---
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM categories WHERE id = $id";
    
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('ลบข้อมูลสำเร็จ'); window.location='categories.php';</script>";
    } else {
        echo "<script>alert('ลบไม่ได้! เนื่องจากมีหนังสือใช้งานหมวดหมู่นี้อยู่'); window.location='categories.php';</script>";
    }
}

// --- ส่วนที่ 3: ดึงข้อมูล ---
$sql = "SELECT * FROM categories ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>จัดการหมวดหมู่หนังสือ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>🏷️ จัดการหมวดหมู่หนังสือ</h2>
            <a href="index.php" class="btn btn-secondary">🏠 กลับหน้าหลัก</a>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">เพิ่มหมวดหมู่ใหม่</div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label>ชื่อหมวดหมู่ / ซีรีส์</label>
                                <input type="text" name="name" class="form-control" placeholder="เช่น การ์ตูน, นิยายผี" required>
                            </div>
                            <button type="submit" name="save_category" class="btn btn-success w-100">บันทึก</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">รายการหมวดหมู่ทั้งหมด</div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ชื่อหมวดหมู่</th>
                                    <th width="100">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result->num_rows > 0): ?>
                                    <?php while($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td>
                                                <?php 
                                                // กันพลาด: เช็คทั้งตัวเล็กตัวใหญ่
                                                if(isset($row['NAME'])) echo $row['NAME'];
                                                else if(isset($row['name'])) echo $row['name']; 
                                                ?>
                                            </td>
                                            
                                            <td class="text-center">
                                                <a href="categories.php?delete=<?php echo $row['id']; ?>" 
                                                   class="btn btn-danger btn-sm" 
                                                   onclick="return confirm('ยืนยันที่จะลบหมวดหมู่นี้?');">ลบ</a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="2" class="text-center">ยังไม่มีหมวดหมู่</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>