<?php include 'db.php'; ?>
<?php include 'header.php'; ?>
<?php

// Ensure guest_id is correctly set in the session or cookies
$guest_id = $_COOKIE['guest_id'] ?? null;
if (!$guest_id) {
    echo "No guest ID found. Please try again.";
    exit;
}

// Add product to cart
if ($_GET['action'] == 'add') {
    $id = $_GET['id'];

    // Check if product already in guest cart
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

// Remove product from cart
if ($_GET['action'] == 'remove') {
    $id = $_GET['id'];
    $conn->prepare("DELETE FROM guest_carts WHERE guest_id=? AND product_id=?")
         ->execute([$guest_id, $id]);
}
?>
<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Your Cart</title>
</head>
<body>
<h2>Your Shopping Cart</h2>
<?php
// Fetch the items in the guest's cart
$stmt = $conn->prepare("
    SELECT p.*, gc.quantity 
    FROM guest_carts gc 
    JOIN products p ON gc.product_id = p.id 
    WHERE gc.guest_id = ?
");
$stmt->execute([$guest_id]);
if ($stmt->rowCount() > 0) {
    echo "Found items in cart."; // Debugging line
    while ($item = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Your code to display items
    }
} else {
    echo "<p>Your cart is empty.</p>";
}
// Check if the cart has any items
if ($stmt->rowCount() > 0) {
    $total = 0;

    // Loop through each product in the cart
    while ($item = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $sub = $item['price'] * $item['quantity'];
        $total += $sub;

        // Display each product in the cart
        echo "<p>{$item['name']} x {$item['quantity']} = RM $sub 
              <a href='cart.php?action=remove&id={$item['product_id']}'>Remove</a></p>";
    }
    echo "<h3>Total: RM $total</h3>";
} else {
    echo "<p>Your cart is empty.</p>";
}
?>
<a href="checkout.php">Checkout</a> | <a href="index.php">Continue Shopping</a>
<?php include 'footer.php'; ?>
</body>
</html>
