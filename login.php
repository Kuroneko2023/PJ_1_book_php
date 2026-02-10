<?php
session_start(); // เริ่มต้น Session (สำคัญมาก!)
require_once 'db_connect.php';

// ถ้ามี Session อยู่แล้ว (Login แล้ว) ให้เด้งไปหน้าแรกเลย
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// ตรวจสอบเมื่อกดปุ่ม Login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // ค้นหา username ใน Database
    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // ตรวจสอบรหัสผ่าน (เทียบ Hash)

        if (password_verify($password, $row['PASSWORD'])) {
            // ถ้ารหัสถูก -> สร้างตั๋ว (Session)
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];

            // ส่งไปหน้าแรก
            header("Location: index.php");
            exit();
        } else {
            $error = "รหัสผ่านไม่ถูกต้อง";
        }
    } else {
        $error = "ไม่พบชื่อผู้ใช้งานนี้";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>เข้าสู่ระบบ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f0f2f5; height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card { width: 400px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <div class="card">
        <div class="card-header bg-primary text-white text-center">
            <h4>🔐 เข้าสู่ระบบ</h4>
        </div>
        <div class="card-body p-4">
            <?php if(isset($error)): ?>
                <div class="alert alert-danger text-center"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label>ชื่อผู้ใช้งาน</label>
                    <input type="text" name="username" class="form-control" required autofocus>
                </div>
                <div class="mb-3">
                    <label>รหัสผ่าน</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
        </div>
    </div>
</body>
</html>