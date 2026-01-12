<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'includes/config.php';
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('กรุณาเข้าสู่ระบบก่อน'); window.location='login.php';</script>";
    exit();
}

$user_id = $_SESSION['user_id'];

// --- ส่วนที่ 1: บันทึกการสั่งซื้อใหม่ลง Database ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_order'])) {
    $total = (float)$_POST['total_price'];
    $payment = mysqli_real_escape_string($conn, $_POST['payment_method']);
    $status = ($payment == "โอนเงิน") ? "รอการโอนเงิน" : "รอจัดส่ง (COD)";

    // บันทึกลงตาราง orders
    $sql = "INSERT INTO orders (user_id, total, status, payment_method, created_at) VALUES (?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("idss", $user_id, $total, $status, $payment);
    
    if ($stmt->execute()) {
        $order_id = $conn->insert_id;
        // บันทึกรายละเอียดสินค้าลงตาราง order_items (ถ้ามีตารางแยก)
        if (isset($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $item) {
                $p_name = $item['name'];
                $p_price = $item['price'];
                $sql_item = "INSERT INTO order_items (order_id, product_name, price) VALUES (?, ?, ?)";
                $stmt_i = $conn->prepare($sql_item);
                $stmt_i->bind_param("isd", $order_id, $p_name, $p_price);
                $stmt_i->execute();
            }
        }
        unset($_SESSION['cart']); // สั่งซื้อเสร็จแล้วล้างตะกร้า
        echo "<script>alert('สั่งซื้อสำเร็จ! หมายเลขคำสั่งซื้อ #$order_id');</script>";
    }
}

// --- ส่วนที่ 2: ดึงข้อมูลประวัติการสั่งซื้อมาแสดง ---
$sql_history = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC";
$stmt_h = $conn->prepare($sql_history);
$stmt_h->bind_param("i", $user_id);
$stmt_h->execute();
$result = $stmt_h->get_result();
?>

<style>
    .orders-container { max-width: 1000px; margin: 40px auto; padding: 20px; font-family: 'Kanit'; }
    .order-card { background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 15px; border-left: 5px solid #2e86c1; }
    .status-badge { padding: 5px 12px; border-radius: 20px; font-size: 13px; font-weight: bold; }
    .status-pending { background: #fff3cd; color: #856404; }
    .status-success { background: #d4edda; color: #155724; }
</style>

<div class="orders-container">
    <h2>📋 ประวัติการสั่งซื้อของฉัน</h2>
    <?php if ($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
            <div class="order-card">
                <div style="display:flex; justify-content:space-between;">
                    <strong>หมายเลขคำสั่งซื้อ #<?= $row['id'] ?></strong>
                    <span class="status-badge <?= ($row['status'] == 'สำเร็จ') ? 'status-success' : 'status-pending' ?>">
                        <?= $row['status'] ?>
                    </span>
                </div>
                <p style="margin: 10px 0;">วันที่สั่งซื้อ: <?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></p>
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <span style="color:#2e86c1; font-weight:bold; font-size:18px;">ยอดรวม: <?= number_format($row['total'], 2) ?> บาท</span>
                    <a href="order_detail.php?id=<?= $row['id'] ?>" style="text-decoration:none; color:#555;">ดูรายละเอียด ></a>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p style="text-align:center; color:#888;">ยังไม่มีประวัติการสั่งซื้อ</p>
    <?php endif; ?>
</div>
<?php include 'includes/footer.php'; ?>