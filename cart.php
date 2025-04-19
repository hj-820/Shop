<?php include 'db.php'; ?>
<?php include 'header.php'; ?>

<?php
$guest_id = $_COOKIE['guest_id'] ?? null;
if (!$guest_id) {
    $guest_id = uniqid('guest_', true);
    setcookie('guest_id', $guest_id, time() + (86400 * 30), "/");
}

$action = $_GET['action'] ?? '';
$product_id = $_GET['id'] ?? '';
$product_type = $_GET['type'] ?? '';

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
    header("Location: cart.php");
    exit;
}

if ($action === 'remove' && $product_id && $product_type) {
    $conn->prepare("DELETE FROM guest_carts WHERE guest_id=? AND product_id=? AND product_type=?")
         ->execute([$guest_id, $product_id, $product_type]);
    header("Location: cart.php");
    exit;
}

if ($action === 'increase') {
    $conn->prepare("UPDATE guest_carts SET quantity = quantity + 1 WHERE guest_id=? AND product_id=? AND product_type=?")
         ->execute([$guest_id, $product_id, $product_type]);
    header("Location: cart.php");
    exit;
}

if ($action === 'decrease') {
    $stmt = $conn->prepare("SELECT quantity FROM guest_carts WHERE guest_id=? AND product_id=? AND product_type=?");
    $stmt->execute([$guest_id, $product_id, $product_type]);
    $qty = $stmt->fetchColumn();

    if ($qty > 1) {
        $conn->prepare("UPDATE guest_carts SET quantity = quantity - 1 WHERE guest_id=? AND product_id=? AND product_type=?")
             ->execute([$guest_id, $product_id, $product_type]);
    } else {
        $conn->prepare("DELETE FROM guest_carts WHERE guest_id=? AND product_id=? AND product_type=?")
             ->execute([$guest_id, $product_id, $product_type]);
    }
    header("Location: cart.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Your Cart</title>
    <link rel="stylesheet" href="style.css">
    <style>
        h2 {
            text-align: center;
            margin-bottom: 30px;
        }

        h3 {
            text-align: right;
        }
    </style>
</head>
<body>
<div class="cart-container">
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

            $productStmt = $conn->prepare("SELECT * FROM `$type` WHERE id = ?");
            $productStmt->execute([$pid]);
            $product = $productStmt->fetch(PDO::FETCH_ASSOC);

            if ($product) {
                $sub = $product['price'] * $qty;
                $total += $sub;
                echo "
                <div class='cart-item'>
                    <img src='{$product['img_url']}' alt='{$product['name']}'>
                    <div class='cart-item-details'>
                        <strong>{$product['name']}</strong><br>
                        RM {$product['price']} each
                        <div class='quantity-controls'>
                            <a href='cart.php?action=decrease&id=$pid&type=$type'>-</a>
                            <div class='quantity-box'>{$qty}</div>
                            <a href='cart.php?action=increase&id=$pid&type=$type'>+</a>
                            <a class='remove-link' href='cart.php?action=remove&id=$pid&type=$type'>Remove</a>
                        </div>
                    </div>
                </div>
                ";
            }
        }

        echo "<h3>Total: RM $total</h3>";
    } else {
        echo "<p>Your cart is empty.</p>";
    }
    ?>

    <div class="cart-buttons">
        <a href="products.php">Continue Shopping</a>
        <a href="checkout.php">Checkout</a>
    </div>
</div>

<?php include 'footer.php'; ?>
</body>
</html>
