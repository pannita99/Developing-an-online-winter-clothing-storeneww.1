<?php
// 1. เริ่ม Session เพื่อเก็บสถานะการเข้าสู่ระบบ
if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
}

include 'includes/config.php'; // ไฟล์นี้ต้องมีตัวแปร $conn
include 'includes/header.php';

$error = ""; 

// 2. ตรวจสอบเมื่อมีการกดปุ่ม "เข้าสู่ระบบ"
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // ป้องกัน SQL Injection ด้วยการ Escape ข้อมูล
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password']; 

    if (!empty($email) && !empty($password)) {
        // ค้นหาผู้ใช้จาก Email
        $sql = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            
            /* ✅ แก้ไขจุดนี้: ตรวจสอบรหัสผ่าน 
               ใช้ password_verify() หากคุณเก็บรหัสแบบ password_hash() ในฐานข้อมูล
               แต่ถ้ายังเก็บแบบข้อความธรรมดา ให้ใช้: if ($password === $user['password'])
            */
            if (password_verify($password, $user['password']) || $password === $user['password']) {
                
                // ✅ เข้าสู่ระบบสำเร็จ: เก็บค่าสำคัญลง Session
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_name'] = $user['name'];
                
                // ส่งไปหน้าแรก (index.php)
                header("Location: index.php");
                exit();
            } else {
                $error = "รหัสผ่านไม่ถูกต้อง";
            }
        } else {
            $error = "ไม่พบอีเมลนี้ในระบบ";
        }
    } else {
        $error = "กรุณากรอกข้อมูลให้ครบถ้วน";
    }
}
?>

<style>
    /* CSS ตกแต่งฟอร์ม Login */
    .login-container {
        max-width: 400px;
        margin: 100px auto;
        padding: 30px;
        background: #fff;
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        font-family: 'Kanit', sans-serif;
    }
    .login-container h2 { text-align: center; color: #2e86c1; margin-bottom: 25px; }
    .login-container input {
        width: 100%;
        padding: 12px;
        margin-bottom: 15px;
        border: 1px solid #ddd;
        border-radius: 8px;
        box-sizing: border-box;
    }
    .btn-login {
        width: 100%;
        padding: 12px;
        background: #2e86c1;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        cursor: pointer;
        transition: 0.3s;
    }
    .btn-login:hover { background: #21618c; }
    .error-msg { 
        color: #e74c3c; 
        background: #fdf2f2; 
        padding: 10px; 
        border-radius: 5px; 
        margin-bottom: 15px; 
        text-align: center; 
        font-size: 14px; 
    }
</style>

<div class="login-container">
    <h2>เข้าสู่ระบบ</h2>

    <?php if ($error != ""): ?>
        <div class="error-msg"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="post" action="">
        <input type="email" name="email" placeholder="อีเมล" required>
        <input type="password" name="password" placeholder="รหัสผ่าน" required>
        <button type="submit" class="btn-login">เข้าสู่ระบบ</button>
    </form>
    
    <p style="text-align: center; margin-top: 15px; font-size: 14px;">
        ยังไม่มีบัญชี? <a href="register.php" style="color: #2e86c1;">สมัครสมาชิก</a>
    </p>
</div>

<?php include 'includes/footer.php'; ?>