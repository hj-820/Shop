<?php
session_start();
include 'db.php';
include 'header.php';

if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

if ($_GET['action'] == 'add') {
    $id = $_GET['id'];
    $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + 1;
}

if ($_GET['action'] == 'remove') {
    $id = $_GET['id'];
    unset($_SESSION['cart'][$id]);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Your Cart</title>
</head>
<body>
<h2>Your Shopping Cart</h2>
<?php
$total = 0;
foreach ($_SESSION['cart'] as $id => $qty) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE id=?");
    $stmt->execute([$id]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);
    $sub = $item['price'] * $qty;
    $total += $sub;
    echo "<p>{$item['name']} x $qty = RM $sub 
          <a href='cart.php?action=remove&id=$id'>Remove</a></p>";
}
echo "<h3>Total: RM $total</h3>";
?>
<a href="checkout.php">Checkout</a> | <a href="index.php">Continue Shopping</a>
<?php include 'footer.php'; ?>
</body>
</html>
