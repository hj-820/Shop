<?php

// Generate or get existing guest ID
if (!isset($_COOKIE['guest_id'])) {
    $guest_id = uniqid('guest_', true);
    setcookie('guest_id', $guest_id, time() + (86400 * 30), "/"); // valid 30 days
} else {
    $guest_id = $_COOKIE['guest_id'];
}

include 'db.php';
include 'header.php';

if (isset($_GET['action']) && $_GET['action'] == 'add') {
    $id = $_GET['id'];

    $stmt = $conn->prepare("SELECT * FROM guest_carts WHERE guest_id=? AND product_id=?");
    $stmt->execute([$guest_id, $id]);

    if ($stmt->rowCount() > 0) {
        $conn->prepare("UPDATE guest_carts SET quantity = quantity + 1 WHERE guest_id=? AND product_id=?")
             ->execute([$guest_id, $id]);
    } else {
        $conn->prepare("INSERT INTO guest_carts (guest_id, product_id, quantity) VALUES (?, ?, 1)")
             ->execute([$guest_id, $id]);
    }
}

if (isset($_GET['action']) && $_GET['action'] == 'remove') {
    $id = $_GET['id'];
    $conn->prepare("DELETE FROM guest_carts WHERE guest_id=? AND product_id=?")
         ->execute([$guest_id, $id]);
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
$stmt = $conn->prepare("
    SELECT p.*, gc.quantity 
    FROM guest_carts gc 
    JOIN products p ON gc.product_id = p.id 
    WHERE guest_id=?
");
$stmt->execute([$guest_id]);
$items = $stmt->fetchAll();

$total = 0;
if ($items) {
    foreach ($items as $item) {
        $sub = $item['price'] * $item['quantity'];
        $total += $sub;
        echo "<p>{$item['name']} x {$item['quantity']} = RM $sub 
              <a href='cart.php?action=remove&id={$item['id']}'>Remove</a></p>";
    }
    echo "<h3>Total: RM $total</h3>";
    echo '<a href="checkout.php">Checkout</a> | <a href="index.php">Continue Shopping</a>';
} else {
    echo "<p>Your cart is empty.</p>";
}
?>
<?php include 'footer.php'; ?>
</body>
</html>
