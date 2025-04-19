<?php
session_start();
include 'db.php';
include 'header.php';

echo "<h2>Thank you for your purchase!</h2>";
$_SESSION['cart'] = []; // Clear cart
echo "<a href='index.php'>Back to Shop</a>";
?>
<?php include 'footer.php'; ?>
