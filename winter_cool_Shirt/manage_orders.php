<?php
include 'includes/config.php';
include 'includes/auth.php';
allowRoles(['admin_main','admin_sub']);
include 'includes/header.php';

$result = mysqli_query($conn,"SELECT orders.*, users.fullname FROM orders JOIN users ON orders.user_id = users.user_id");

echo "<h2>คำสั่งซื้อทั้งหมด</h2>";
while($r=mysqli_fetch_assoc($result)){
    echo "<div class='card'>";
    echo "หมายเลขคำสั่งซื้อ: ".$r['order_id']."<br>";
    echo "ลูกค้า: ".$r['fullname']."<br>";
    echo "ยอดรวม: ".$r['total']." บาท<br>";
    echo "สถานะ: ".$r['status']."<br>";
    echo "</div>";
}

include 'includes/footer.php';
