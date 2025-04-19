<?php include 'db.php'; ?>
<?php include 'header.php'; ?>

<?php
// Ensure guest_id is correctly set in the cookie
$guest_id = $_COOKIE['guest_id'] ?? null;
if (!$guest_id) {
    $guest_id = uniqid('guest_', true);
    setcookie('guest_id', $guest_id, time() + (86400 * 30), "/"); // valid for 30 days
}

// Get product info from URL
$action = $_GET['action'] ?? '';
$product_id = $_GET['id'] ?? '';
$product_type = $_GET['type'] ?? ''; // must pass ?type=flowers|bears|photos

// Add product to cart
if ($action === 'add' && $product_id && $product_type) {
    $stmt = $conn->prepare("SELECT * FROM guest_carts WHERE guest_id=? AND product_id=? AND product_type=?");
    $stmt->execute([$guest_id, $product_id, $product_type]);

    if ($stmt->rowCount() > 0) {
        $conn->prepare("UPDATE guest_carts SET quantity = quantity + 1 WHERE guest_id=? AND product_id=? AND product_type=?")
             ->execute([$guest_id, $product_id, $product_type]);
    } else {
        $conn->prepare("INSERT INTO guest_carts (guest_id, product_id, product_type, quantity) VALUES (?, ?, ?, 1)")
             ->execute([$guest_id, $product_id, $product_type]);
    }
}

// Remove product from cart
if ($action === 'remove' && $product_id && $product_type) {
    $conn->prepare("DELETE FROM guest_carts WHERE guest_id=? AND product_id=? AND product_type=?")
         ->execute([$guest_id, $product_id, $product_type]);
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
$stmt = $conn->prepare("SELECT * FROM guest_carts WHERE guest_id = ?");
$stmt->execute([$guest_id]);
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($cartItems) > 0) {
    $total = 0;

    foreach ($cartItems as $item) {
        $type = $item['product_type'];
        $pid = $item['product_id'];
        $qty = $item['quantity'];

        // Dynamically fetch from the correct table
        $productStmt = $conn->prepare("SELECT * FROM `$type` WHERE id = ?");
        $productStmt->execute([$pid]);
        $product = $productStmt->fetch(PDO::FETCH_ASSOC);

        if ($product) {
            $sub = $product['price'] * $qty;
            $total += $sub;

            echo "<p>{$product['name']} x $qty = RM $sub 
                  <a href='cart.php?action=remove&id=$pid&type=$type'>Remove</a></p>";
        }
    }

    echo "<h3>Total: RM $total</h3>";
} else {
    echo "<p>Your cart is empty.</p>";
}
?>

<a href="checkout.php">Checkout</a> | <a href="products.php">Continue Shopping</a>
<?php include 'footer.php'; ?>
</body>
</html>
