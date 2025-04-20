<?php
include 'db.php';
include 'header.php';

$guest_id = $_COOKIE['guest_id'] ?? null;
if (!$guest_id) {
    echo "<p>No guest ID found.</p>";
    include 'footer.php';
    exit;
}

$orderComplete = false;
$cartItems = [];
$name = $phone = $email = $address = $payment = '';
$total = 0;

// Handle order submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $name = $_POST['name'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $email = $_POST['email'] ?? '';
    $address = $_POST['address'] ?? '';
    $payment = $_POST['payment'] ?? '';

    $stmt = $conn->prepare("SELECT * FROM guest_carts WHERE guest_id = ?");
    $stmt->execute([$guest_id]);
    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Clear the cart after order is placed
    $stmt = $conn->prepare("DELETE FROM guest_carts WHERE guest_id = ?");
    $stmt->execute([$guest_id]);

    $orderComplete = true;
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
        .order-summary {
            max-width: 700px;
            margin: 40px auto;
            padding: 30px;
            border: 1px solid #ccc;
            border-radius: 15px;
            background-color: #f9f9f9;
            font-family: Arial, sans-serif;
        }

        .order-summary h2, .order-summary h3 {
            text-align: center;
            color: #333;
        }

        .order-items {
            margin: 20px 0;
        }

        .order-item {
            padding: 10px 0;
            border-bottom: 1px solid #ddd;
        }

        .order-summary p {
            line-height: 1.6;
        }

        .back-to-shop {
            display: inline-block;
            margin-top: 25px;
            padding: 12px 24px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: bold;
            text-align: center;
        }

        .back-to-shop:hover {
            background-color: #0056b3;
        }

        .checkout-container {
            max-width: 700px;
            margin: 40px auto;
            padding: 30px;
            font-family: Arial, sans-serif;
        }

        label {
            display: block;
            margin: 10px 0 5px;
        }

        input[type="text"], input[type="email"], textarea {
            width: 100%;
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .payment-options {
            margin: 10px 0;
        }

        .buttons {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
        }

        .place, .cancel {
            padding: 12px 24px;
            font-weight: bold;
            border: none;
            border-radius: 10px;
            color: white;
            cursor: pointer;
        }

        .place {
            background-color: green;
        }

        .cancel {
            background-color: red;
        }

        .place:hover {
            background-color: darkgreen;
        }

        .cancel:hover {
            background-color: darkred;
        }

        .cart-item {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 15px 0;
        }

        .cart-item img {
            width: 80px;
            height: 80px;
            object-fit: cover;
        }

        .item-details {
            flex-grow: 1;
        }
    </style>
</head>
<body>
<div class="checkout-container">

<?php if ($orderComplete): ?>
    <div class="order-summary">
        <h2>Thank you for your purchase, <?= htmlspecialchars($name) ?>!</h2>
        <h3>Order Summary:</h3>
        <div class="order-items">
            <?php
            foreach ($cartItems as $item):
                $type = $item['product_type'];
                $pid = $item['product_id'];
                $qty = $item['quantity'];

                $productStmt = $conn->prepare("SELECT * FROM `$type` WHERE id = ?");
                $productStmt->execute([$pid]);
                $product = $productStmt->fetch(PDO::FETCH_ASSOC);

                if ($product):
                    $sub = $product['price'] * $qty;
                    $total += $sub;
            ?>
            <div class="order-item">
                <strong><?= htmlspecialchars($product['name']) ?></strong><br>
                RM <?= $product['price'] ?> x <?= $qty ?> = <strong>RM <?= $sub ?></strong>
            </div>
            <?php endif; endforeach; ?>
        </div>

        <h3>Total Purchase: RM <?= $total ?></h3>

        <h3>Customer Details:</h3>
        <p><strong>Name:</strong> <?= htmlspecialchars($name) ?></p>
        <p><strong>Phone:</strong> <?= htmlspecialchars($phone) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($email) ?></p>
        <p><strong>Address:</strong> <?= nl2br(htmlspecialchars($address)) ?></p>
        <p><strong>Payment Method:</strong> <?= htmlspecialchars($payment) ?></p>

        <a class="back-to-shop" href="products.php">Back to Shop</a>
    </div>
<?php else: ?>
    <h2>Checkout</h2>

    <?php
    $stmt = $conn->prepare("SELECT * FROM guest_carts WHERE guest_id = ?");
    $stmt->execute([$guest_id]);
    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($cartItems) > 0) {
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
<?php endif; ?>
</div>
<?php include 'footer.php'; ?>
</body>
</html>
