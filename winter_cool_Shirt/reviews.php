<?php include 'includes/config.php'; ?>
<?php include 'includes/header.php'; ?>

<?php
$pid = $_GET['product_id'];
$result = mysqli_query($conn,"SELECT reviews.*, users.fullname FROM reviews JOIN users ON reviews.user_id = users.user_id WHERE product_id=$pid");
?>

<h2>รีวิวสินค้า</h2>

<?php while($r=mysqli_fetch_assoc($result)){ ?>
<div class="card">
<strong><?php echo $r['fullname'];?></strong><br>
คะแนน: <?php echo $r['rating'];?>/5
<p><?php echo $r['comment'];?></p>
</div>
<?php } ?>

<?php include 'includes/footer.php'; ?>
