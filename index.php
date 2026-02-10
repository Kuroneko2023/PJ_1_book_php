<?php
session_start();
require_once 'db_connect.php';

// --- หมายเหตุ: ลบ require_once 'auth.php' ออก เพื่อให้คนทั่วไปดูรายการหนังสือได้ ---

// --- 1. เตรียมตัวแปรสำหรับค้นหา ---
$search_text = "";
$search_category = "";
$where_conditions = array();

if (isset($_GET['search_text'])) {
    $search_text = $_GET['search_text'];
    $where_conditions[] = "(title LIKE '%$search_text%' OR isbn LIKE '%$search_text%')";
}

if (isset($_GET['category_id']) && $_GET['category_id'] != "") {
    $search_category = $_GET['category_id'];
    $where_conditions[] = "category_id = '$search_category'";
}

// --- 2. สร้างคำสั่ง SQL ---
$sql = "SELECT books.*, categories.name AS category_name 
        FROM books 
        LEFT JOIN categories ON books.category_id = categories.id";

if (count($where_conditions) > 0) {
    $sql .= " WHERE " . implode(' AND ', $where_conditions);
}

$sql .= " ORDER BY books.created_at DESC";
$result = $conn->query($sql);

// --- 3. ดึงหมวดหมู่มาใส่ใน Dropdown ตัวกรอง ---
$cat_sql = "SELECT * FROM categories ORDER BY name ASC";
$cat_result = $conn->query($cat_sql);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ระบบร้านหนังสือ (PJ_1_Book)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
            <h1>📚 ระบบจัดการร้านหนังสือ</h1>
            <div>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="dashboard.php" class="btn btn-info btn-sm text-white me-2">
                    📊 ดูภาพรวม
                </a>

                <span class="me-2 text-muted">
                    ผู้ใช้งาน: <strong><?php echo $_SESSION['username']; ?></strong>
                </span>
                <a href="logout.php" class="btn btn-danger btn-sm">🚪 ออกจากระบบ</a>
                
            <?php else: ?>
                <a href="login.php" class="btn btn-primary btn-sm">🔐 เข้าสู่ระบบ (Admin)</a>
            <?php endif; ?>
        </div>
            
        </div>
        
        <div class="card mb-4 bg-light">
            <div class="card-body">
                <form method="GET" action="" class="row g-2 align-items-center">
                    <div class="col-md-4">
                        <label class="form-label">ค้นหาชื่อ / ISBN</label>
                        <input type="text" name="search_text" class="form-control" 
                               placeholder="พิมพ์ชื่อหนังสือ..." value="<?php echo htmlspecialchars($search_text); ?>">
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label">หมวดหมู่</label>
                        <select name="category_id" class="form-select">
                            <option value="">-- ทั้งหมด --</option>
                            <?php 
                            if ($cat_result->num_rows > 0) {
                                $cat_result->data_seek(0);
                                while($cat = $cat_result->fetch_assoc()) {
                                    $selected = ($cat['id'] == $search_category) ? "selected" : "";
                                    // ดึงชื่อ (รองรับทั้งตัวเล็กตัวใหญ่)
                                    $cat_name = isset($cat['NAME']) ? $cat['NAME'] : $cat['name'];
                                    echo '<option value="'.$cat['id'].'" '.$selected.'>'.$cat_name.'</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="col-md-4 align-self-end">
                        <button type="submit" class="btn btn-primary">🔍 ค้นหา</button>
                        <a href="index.php" class="btn btn-secondary">ล้างค่า</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>รายการหนังสือในสต็อก</h3>
            <div>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="categories.php" class="btn btn-outline-primary me-2">🏷️ จัดการหมวดหมู่</a>
                    <a href="add.php" class="btn btn-success">+ เพิ่มหนังสือใหม่</a>
                <?php endif; ?>
            </div>
        </div>

        <table class="table table-bordered table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th width="100">รูปภาพ</th>
                    <th>หมวดหมู่</th>
                    <th>รหัส (ISBN)</th>
                    <th>ชื่อหนังสือ</th>
                    <th>ราคา</th>
                    <th>สต็อก</th>
                    <th>จัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td class="text-center">
                                <?php if(!empty($row['image'])): ?>
                                    <img src="uploads/<?php echo $row['image']; ?>" width="80" class="img-thumbnail">
                                <?php else: ?>
                                    <span class="text-muted">ไม่มีรูป</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-info text-dark">
                                    <?php echo isset($row['category_name']) ? $row['category_name'] : '-'; ?>
                                </span>
                            </td>
                            <td><?php echo $row['isbn']; ?></td>
                            <td>
                                <strong><?php echo $row['title']; ?></strong><br>
                                <small class="text-muted">ผู้แต่ง: <?php echo $row['author']; ?></small>
                            </td>
                            <td><?php echo number_format($row['price'], 2); ?></td>
                            <td>
                                <?php 
                                    if($row['stock'] < 5) echo "<span class='text-danger fw-bold'>" . $row['stock'] . " (ของใกล้หมด)</span>";
                                    else echo $row['stock'];
                                ?>
                            </td>
                            <td>
                                <?php if (isset($_SESSION['user_id'])): ?>
                                    <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">แก้ไข</a>
                                    <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('ยืนยันการลบ?');">ลบ</a>
                                <?php else: ?>
                                    <span class="text-muted small">🔒 เฉพาะ Admin</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="7" class="text-center text-danger p-4">ไม่พบข้อมูลหนังสือที่ค้นหา</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>