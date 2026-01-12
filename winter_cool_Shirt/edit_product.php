<?php
include 'includes/config.php';
include 'includes/auth.php';
allowRoles(['admin_main']);
include 'includes/header.php'];

$id = $_GET['id'];
$p = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM products WHERE product_id=$id"));

if($_SERVER['REQUEST_METHOD']=='POST'){
    $name = $_POST['name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];

    if($_FILES['image']['name']!=""){
        $image = $_FILES['image']['name'];
        $tmp = $_FILES['image']['tmp_name'];
        $ext = pathinfo($image, PATHINFO_EXTENSION);
        $new_image = time().".".$ext;
        move_uploaded_file($tmp,"uploads/".$new_image);
        $img_sql = ", image='$new_image'";
    }else{$img_sql="";}

    mysqli_query($conn,"UPDATE products SET name='$name', description='$desc', price='$price', stock='$stock' $img_sql WHERE product_id=$id");
    echo "<p style='color:green'>แก้ไขเรียบร้อย</p>";
}

?>

<h2>แก้ไขสินค้า</h2>
<form method="post" enctype="multipart/form-data">
<input name="name" value="<?php echo $p['name'];?>" required>
<textarea name="description" required><?php echo $p['description'];?></textarea>
<input name="price" value="<?php echo $p['price'];?>" required>
<input name="stock" value="<?php echo $p['stock'];?>" required>
<input type="file" name="image">
<button>บันทึก</button>
</form>

<?php include 'includes/footer.php'; ?>
