<?php
session_start();
include 'db.php';

echo "<h2>Thank you for your purchase!</h2>";
$_SESSION['cart'] = []; // Clear cart
echo "<a href='index.php'>Back to Shop</a>";
?>
