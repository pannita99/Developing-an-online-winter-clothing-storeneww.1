<?php
// 1. เริ่ม Session และเชื่อมต่อไฟล์ที่จำเป็น
if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
}

include 'includes/config.php'; // ไฟล์นี้ต้องมีตัวแปร $conn
include 'includes/header.php';

$message = ""; // ไว้แสดงข้อความสำเร็จหรือข้อผิดพลาด

// 2. ตรวจสอบเมื่อมีการกดปุ่ม "สมัครสมาชิก"
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    if (!empty($fullname) && !empty($email) && !empty($password)) {
        
        // ตรวจสอบก่อนว่าอีเมลนี้มีในระบบหรือยัง
        $check_email = "SELECT email FROM users WHERE email = '$email'";
        $result = mysqli_query($conn, $check_email);

        if (mysqli_num_rows($result) > 0) {
            $message = "<div class='error-msg'>❌ อีเมลนี้ถูกใช้ไปแล้ว</div>";
        } else {
            // ✅ เข้ารหัสรหัสผ่านเพื่อความปลอดภัย (Hash Password)
            // หมายเหตุ: ถ้าในหน้า Login ใช้แบบข้อความธรรมดา ให้ถอด password_hash ออก
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // บันทึกลงฐานข้อมูล
            $sql = "INSERT INTO users (name, email, password) VALUES ('$fullname', '$email', '$hashed_password')";
            
            if (mysqli_query($conn, $sql)) {
                $message = "<div class='success-msg'>✅ สมัครสมาชิกสำเร็จ! <a href='login.php'>ไปหน้าเข้าสู่ระบบ</a></div>";
            } else {
                $message = "<div class='error-msg'>เกิดข้อผิดพลาด: " . mysqli_error($conn) . "</div>";
            }
        }
    } else {
        $message = "<div class='error-msg'>กรุณากรอกข้อมูลให้ครบทุกช่อง</div>";
    }
}
?>

<style>
    .register-container {
        max-width: 450px;
        margin: 80px auto;
        padding: 35px;
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        font-family: 'Kanit', sans-serif;
    }
    .register-container h2 { text-align: center; color: #2e86c1; margin-bottom: 25px; font-weight: 600; }
    .register-container input {
        width: 100%;
        padding: 14px;
        margin-bottom: 15px;
        border: 1px solid #eee;
        border-radius: 12px;
        box-sizing: border-box;
        background: #f9f9f9;
        font-size: 1rem;
    }
    .register-container input:focus { border-color: #2e86c1; outline: none; background: #fff; }
    .btn-register {
        width: 100%;
        padding: 14px;
        background: #2e86c1;
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 1.1rem;
        font-weight: bold;
        cursor: pointer;
        transition: 0.3s;
    }
    .btn-register:hover { background: #21618c; transform: translateY(-2px); }
    .error-msg { color: #e74c3c; background: #fdf2f2; padding: 12px; border-radius: 10px; margin-bottom: 20px; text-align: center; }
    .success-msg { color: #27ae60; background: #eafaf1; padding: 12px; border-radius: 10px; margin-bottom: 20px; text-align: center; }
</style>



<div class="register-container">
    <h2>สมัครสมาชิกใหม่</h2>

    <?php echo $message; ?>

    <form method="post" action="">
        <input type="text" name="fullname" placeholder="ชื่อ - นามสกุล" required>
        <input type="email" name="email" placeholder="อีเมลที่ใช้งานจริง" required>
        <input type="password" name="password" placeholder="ตั้งรหัสผ่าน" required>
        <button type="submit" class="btn-register">สร้างบัญชีผู้ใช้</button>
    </form>
    
    <p style="text-align: center; margin-top: 20px; color: #777;">
        เป็นสมาชิกอยู่แล้ว? <a href="login.php" style="color: #2e86c1; text-decoration: none; font-weight: 500;">เข้าสู่ระบบ</a>
    </p>
</div>

<?php include 'includes/footer.php'; ?>