<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'includes/config.php';
include 'includes/header.php';

// Logic การเพิ่ม/ลบสินค้า
if (isset($_GET['action']) && $_GET['action'] == 'add') {
    $p_name = isset($_GET['name']) ? trim($_GET['name']) : "";
    $p_price = isset($_GET['price']) ? (float)$_GET['price'] : 0;
    if (!empty($p_name)) {
        if (!isset($_SESSION['cart'])) { $_SESSION['cart'] = []; }
        $_SESSION['cart'][] = ["name" => $p_name, "price" => $p_price];
    }
    header("Location: cart.php"); exit();
}
if (isset($_GET['action']) && $_GET['action'] == 'remove' && isset($_GET['id'])) {
    unset($_SESSION['cart'][(int)$_GET['id']]);
    $_SESSION['cart'] = array_values($_SESSION['cart']);
    header("Location: cart.php"); exit();
}
?>

<style>
    .cart-container { max-width: 900px; margin: 40px auto; padding: 25px; font-family: 'Kanit', sans-serif; background: #fff; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
    .cart-table { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
    .cart-table th { background: #f8f9fa; padding: 15px; text-align: left; border-bottom: 2px solid #dee2e6; }
    .cart-table td { padding: 15px; border-bottom: 1px solid #eee; }
    
    .checkout-section { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px; }
    .address-card, .total-card { background: #f8fafc; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; }
    
    .form-group { margin-bottom: 15px; }
    .form-group label { display: block; margin-bottom: 5px; font-weight: bold; color: #475569; }
    .input-field { width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; font-family: 'Kanit'; box-sizing: border-box; }
    textarea.input-field { min-height: 100px; resize: vertical; }
    
    .payment-select { width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1; margin-bottom: 10px; font-family: 'Kanit'; }
    .total-price { font-size: 32px; color: #e74c3c; font-weight: bold; margin: 10px 0; }
    .checkout-btn { width: 100%; background: #27ae60; color: white; border: none; padding: 15px; border-radius: 30px; font-size: 18px; font-weight: bold; cursor: pointer; transition: 0.3s; }
    .checkout-btn:hover { background: #219150; }

    @media (max-width: 768px) {
        .checkout-section { grid-template-columns: 1fr; }
    }
</style>

<div class="cart-container">
    <h2>🛒 ตะกร้าสินค้าของคุณ</h2>
    
    <?php if (!empty($_SESSION['cart'])): ?>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>ลำดับ</th>
                    <th>ชื่อสินค้า</th>
                    <th style="text-align: right;">ราคา</th>
                    <th style="text-align: center;">จัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php $grand_total = 0; foreach ($_SESSION['cart'] as $index => $item): $grand_total += $item['price']; ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><strong><?= htmlspecialchars($item['name']) ?></strong></td>
                    <td style="text-align: right;"><?= number_format($item['price'], 2) ?> บาท</td>
                    <td style="text-align: center;"><a href="cart.php?action=remove&id=<?= $index ?>" style="color:red; text-decoration: none;">ลบออก</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <form action="orders.php" method="POST">
            <div class="checkout-section">
                <div class="address-card">
                    <h3 style="margin-top:0;"><i class="fas fa-map-marker-alt"></i> ข้อมูลการจัดส่ง</h3>
                    <div class="form-group">
                        <label>ชื่อ-นามสกุล ผู้รับ:</label>
                        <input type="text" name="customer_name" class="input-field" placeholder="ตัวอย่าง: สมชาย ใจดี" required>
                    </div>
                    <div class="form-group">
                        <label>เบอร์โทรศัพท์:</label>
                        <input type="tel" name="customer_phone" class="input-field" placeholder="08X-XXX-XXXX" required>
                    </div>
                    <div class="form-group">
                        <label>ที่อยู่จัดส่งโดยละเอียด:</label>
                        <textarea name="customer_address" class="input-field" placeholder="บ้านเลขที่, ถนน, แขวง/ตำบล, เขต/อำเภอ, จังหวัด, รหัสไปรษณีย์" required></textarea>
                    </div>
                </div>

                <div class="total-card">
                    <h3 style="margin-top:0;"><i class="fas fa-credit-card"></i> การชำระเงิน</h3>
                    
                    <label>เลือกวิธีชำระเงิน:</label>
                    <select name="payment_method" id="pay_method" class="payment-select" onchange="toggleBank()" required>
                        <option value="โอนเงิน">โมบายแบงก์กิ้ง (โอนเงิน)</option>
                        <option value="ชำระเงินปลายทาง">ชำระเงินปลายทาง (COD)</option>
                    </select>

                    <div id="bank_div">
                        <label>เลือกธนาคาร:</label>
                        <select name="bank_name" class="payment-select">
                            <option value="ธนาคารกรุงไทย">ธนาคารกรุงไทย</option>
                            <option value="ธนาคารกรุงเทพ">ธนาคารกรุงเทพ</option>
                            <option value="ธนาคารออมสิน">ธนาคารออมสิน</option>
                        </select>
                    </div>

                    <div style="margin-top: 20px; border-top: 1px solid #e2e8f0; padding-top: 15px; text-align: center;">
                        <p style="margin: 0; color: #64748b;">ยอดชำระสุทธิ:</p>
                        <div class="total-price"><?= number_format($grand_total, 2) ?> ฿</div>
                        
                        <input type="hidden" name="confirm_order" value="1">
                        <input type="hidden" name="total_price" value="<?= $grand_total ?>">
                        <button type="submit" class="checkout-btn">ยืนยันการสั่งซื้อ →</button>
                    </div>
                </div>
            </div>
        </form>

    <?php else: ?>
        <div style="text-align:center; padding: 50px 0;">
            <p style="font-size: 18px; color: #64748b;">ตะกร้าของคุณยังว่างเปล่า</p>
            <a href="index.php" style="color: #3498db; text-decoration: none; font-weight: bold;">← กลับไปเลือกซื้อสินค้า</a>
        </div>
    <?php endif; ?>
</div>

<script>
function toggleBank() {
    var method = document.getElementById("pay_method").value;
    document.getElementById("bank_div").style.display = (method === "โอนเงิน") ? "block" : "none";
}
</script>

<?php include 'includes/footer.php'; ?>