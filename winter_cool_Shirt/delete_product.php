<?php
include 'includes/config.php';
include 'includes/auth.php';
allowRoles(['admin_main']);

$id = $_GET['id'];
mysqli_query($conn,"DELETE FROM products WHERE product_id=$id");
header("Location:products.php");
