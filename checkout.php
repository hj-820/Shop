<?php
include 'db.php';
include 'header.php';

// Get guest ID
$guest_id = $_COOKIE['guest_id'] ?? null;
if (!$guest_id) {
    echo "<p>No guest ID found.</p>";
    exit;
}

// Handle order submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    // Get form input
    $name = $_POST['name'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $email = $_POST['email'] ?? '';
    $address = $_POST['address'] ?? '';
    $payment = $_POST['payment'] ?? '';

    // (Optional) Save to an orders table here

 $stmt = $conn->prepare("SELECT * FROM guest_carts WHERE guest_id = ?");
    $stmt->execute([$guest_id]);
    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Display order summary
    echo "<div class='order-summary'>";
    echo "<h2>Thank you for your purchase, " . htmlspecialchars($name) . "!</h2>";
    echo "<h3>Order Summary:</h3>";

    $total = 0;
    echo "<div class='order-items'>";
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

            echo "<div class='order-item'>
                    <strong>" . htmlspecialchars($product['name']) . "</strong><br>
                    RM {$product['price']} x $qty = <strong>RM $sub</strong>
                  </div>";
        }
    }
    echo "</div>";
    echo "<h3>Total Purchase: RM $total</h3>";

    echo "<h3>Customer Details:</h3>";
    echo "<p><strong>Name:</strong> " . htmlspecialchars($name) . "</p>";
    echo "<p><strong>Phone:</strong> " . htmlspecialchars($phone) . "</p>";
    echo "<p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>";
    echo "<p><strong>Address:</strong> " . nl2br(htmlspecialchars($address)) . "</p>";
    echo "<p><strong>Payment Method:</strong> " . htmlspecialchars($payment) . "</p>";

    echo "<a class='back-to-shop' href='products.php'>Back to Shop</a>";
    echo "</div>";

    // Delete cart
    $stmt = $conn->prepare("DELETE FROM guest_carts WHERE guest_id = ?");
    $stmt->execute([$guest_id]);

    include 'footer.php';
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Checkout</title>
     <link rel="stylesheet" href="style.css">
    <style>
        h2 {
            text-align: center;
        }
        h3 {
            text-align: right;
        }
    </style>
</head>
<body>
<div class="checkout-container">
    <h2>Checkout</h2>

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
                    <div class='item-details'>
                        <strong>{$product['name']}</strong><br>
                        RM {$product['price']} x $qty = <strong>RM $sub</strong>
                    </div>
                </div>";
            }
        }

        echo "<h3>Total: RM $total</h3>";
    } else {
        echo "<p>Your cart is empty.</p>";
    }
    ?>

    <form method="POST" action="checkout.php">
        <label>Name:</label>
        <input type="text" name="name" required>

        <label>Phone:</label>
        <input type="text" name="phone" required>

        <label>Email:</label>
        <input type="email" name="email" required>

        <label>Address:</label>
        <textarea name="address" rows="3" required></textarea>

        <label>Payment Method:</label><br>
        <div class="payment-options">
            <input type="radio" name="payment" value="Credit Card" required> Credit Card<br>
            <input type="radio" name="payment" value="Online Banking"> Online Banking<br>
            <input type="radio" name="payment" value="E-Wallet"> E-Wallet
        </div>

        <div class="buttons">
            <button type="button" class="cancel" onclick="window.location.href='products.php'">Cancel Checkout</button>
            <button type="submit" name="place_order" class="place">Place Order</button>
        </div>
    </form>
</div>
<?php include 'footer.php'; ?>
</body>
</html>
