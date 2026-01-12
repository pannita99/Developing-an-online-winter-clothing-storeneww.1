<?php
include 'includes/config.php';
include 'includes/auth.php';
allowRoles(['admin_main','admin_sub']);
include 'includes/header.php';

if($_SERVER['REQUEST_METHOD']=='POST'){
    $name = $_POST['name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];

    $image = $_FILES['image']['name'];
    $tmp = $_FILES['image']['tmp_name'];
    $ext = pathinfo($image, PATHINFO_EXTENSION);
    $new_image = time().".".$ext;
    move_uploaded_file($tmp,"uploads/".$new_image);

    mysqli_query($conn,"INSERT INTO products(name,description,price,stock,image) VALUES('$name','$desc','$price','$stock','$new_image')");
    echo "<p style='color:green'>เพิ่มสินค้าเรียบร้อย</p>";
}
?>

<h2>เพิ่มสินค้า</h2>
<form method="post" enctype="multipart/form-data">
<input name="name" placeholder="ชื่อสินค้า" required>
<textarea name="description" placeholder="รายละเอียด" required></textarea>
<input name="price" placeholder="ราคา" required>
<input name="stock" placeholder="จำนวน" required>
<input type="file" name="image" required>
<button>เพิ่มสินค้า</button>
</form>

<?php include 'includes/footer.php'; ?>
