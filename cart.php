<?php
ob_start(); // Allow setting cookies
session_start();
include 'db.php';
include 'header.php';

// Generate or retrieve guest_id
if (!isset($_COOKIE['guest_id'])) {
    $guest_id = uniqid('guest_', true);
    setcookie('guest_id', $guest_id, time() + (86400 * 30), "/"); // 30 days
} else {
    $guest_id = $_COOKIE['guest_id'];
}

// Handle Add to Cart
if (isset($_GET['action']) && $_GET['action'] == 'add' && isset($_GET['id'])) {
    $id = $_GET['id'];

    // Check if item already in cart
    $stmt = $conn->prepare("SELECT * FROM guest_carts WHERE guest_id=? AND product_id=?");
    $stmt->execute([$guest_id, $id]);

    if ($stmt->rowCount() > 0) {
        $conn->prepare("UPDATE guest_carts SET quantity = quantity + 1 WHERE guest_id=? AND product_id=?")
             ->execute([$guest_id, $id]);
    } else {
        $conn->prepare("INSERT INTO guest_carts (guest_id, product_id, quantity) VALUES (?, ?, 1)")
             ->execute([$guest_id, $id]);
    }

    // Redirect to avoid resubmission
    header("Location: cart.php");
    exit();
}

// Handle Remove from Cart
if (isset($_GET['action']) && $_GET['action'] == 'remove' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $conn->prepare("DELETE FROM guest_carts WHERE guest_id=? AND product_id=?")
         ->execute([$guest_id, $id]);

    // Redirect to avoid resubmission
    header("Location: cart.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Your Cart</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        .cart-item { margin-bottom: 10px; }
        .cart-item a { color: red; text-decoration: none; margin-left: 10px; }
        .total { font-weight: bold; margin-top: 20px; }
    </style>
</head>
<body>
<h2>Your Shopping Cart</h2>
<?php
// Fetch guest cart items
$stmt = $conn->prepare("
    SELECT p.*, gc.quantity 
    FROM guest_carts gc 
    JOIN products p ON gc.product_id = p.id 
    WHERE gc.guest_id = ?
");
$stmt->execute([$guest_id]);
$items = $stmt->fetchAll();

if (count($items) === 0) {
    echo "<p>Your cart is empty.</p>";
} else {
    $total = 0;
    foreach ($items as $item) {
        $sub = $item['price'] * $item['quantity'];
        $total += $sub;
        echo "<div class='cart-item'>{$item['name']} x {$item['quantity']} = RM {$sub}
              <a href='cart.php?action=remove&id={$item['id']}'>Remove</a></div>";
    }
    echo "<div class='total'>Total: RM $total</div>";
}
?>
<br>
<a href="checkout.php">Checkout</a> | <a href="index.php">Continue Shopping</a>
<?php include 'footer.php'; ?>
</body>
</html>
