<?php
session_start();
require_once 'auth.php'; // เรียกยามมาเฝ้า
require_once 'db_connect.php';

// 1. นับจำนวนหนังสือทั้งหมด
$sql_count = "SELECT COUNT(*) as total FROM books";
$res_count = $conn->query($sql_count);
$row_count = $res_count->fetch_assoc();
$total_books = $row_count['total'];

// 2. นับมูลค่าสินค้าในสต็อก (ราคา x จำนวน)
$sql_value = "SELECT SUM(price * stock) as total_value FROM books";
$res_value = $conn->query($sql_value);
$row_value = $res_value->fetch_assoc();
$total_value = $row_value['total_value'];

// 3. หาสินค้าใกล้หมด (น้อยกว่า 5 ชิ้น)
$sql_low = "SELECT COUNT(*) as low_stock FROM books WHERE stock < 5";
$res_low = $conn->query($sql_low);
$row_low = $res_low->fetch_assoc();
$low_stock = $row_low['low_stock'];

// 4. นับหมวดหมู่
$sql_cat = "SELECT COUNT(*) as total_cat FROM categories";
$res_cat = $conn->query($sql_cat);
$row_cat = $res_cat->fetch_assoc();
$total_cat = $row_cat['total_cat'];
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Dashboard ภาพรวมระบบ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>📊 Dashboard ภาพรวมร้านหนังสือ</h1>
            <a href="index.php" class="btn btn-primary">🏠 กลับหน้ารายการสินค้า</a>
        </div>

        <div class="row g-4">
            <div class="col-md-3">
                <div class="card text-white bg-primary h-100 shadow">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-book"></i> หนังสือทั้งหมด</h5>
                        <h2 class="display-4 fw-bold"><?php echo $total_books; ?></h2>
                        <p class="card-text">รายการในระบบ</p>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-white bg-danger h-100 shadow">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-exclamation-triangle"></i> ใกล้หมดสต็อก</h5>
                        <h2 class="display-4 fw-bold"><?php echo $low_stock; ?></h2>
                        <p class="card-text">รายการ (ต้องรีบเติม!)</p>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-white bg-success h-100 shadow">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-cash-coin"></i> มูลค่าสินค้าในคลัง</h5>
                        <h3 class="fw-bold mt-3"><?php echo number_format($total_value); ?></h3>
                        <p class="card-text">บาท</p>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-white bg-info h-100 shadow">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-tags"></i> หมวดหมู่ทั้งหมด</h5>
                        <h2 class="display-4 fw-bold"><?php echo $total_cat; ?></h2>
                        <p class="card-text">ประเภท</p>
                    </div>
                </div>
            </div>
        </div>

        <?php if($low_stock > 0): ?>
        <div class="card mt-5 border-danger shadow-sm">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0">⚠️ รายการที่ต้องรีบเติมสต็อก (เหลือน้อยกว่า 5)</h5>
            </div>
            <div class="card-body">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ISBN</th>
                            <th>ชื่อหนังสือ</th>
                            <th>คงเหลือ</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $sql_alert = "SELECT * FROM books WHERE stock < 5 ORDER BY stock ASC";
                        $res_alert = $conn->query($sql_alert);
                        while($row = $res_alert->fetch_assoc()):
                        ?>
                        <tr>
                            <td><?php echo $row['isbn']; ?></td>
                            <td><?php echo $row['title']; ?></td>
                            <td class="text-danger fw-bold"><?php echo $row['stock']; ?></td>
                            <td>
                                <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger">เติมของ</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

    </div>
</body>
</html>