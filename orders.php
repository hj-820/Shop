s<?php
include 'db.php';
include 'header.php';

$guest_id = $_COOKIE['guest_id'] ?? null;

if (!$guest_id) {
    echo "<p>No guest ID found.</p>";
    include 'footer.php';
    exit;
}

// Get all orders made by this guest
$orderStmt = $conn->prepare("SELECT * FROM orders WHERE guest_id = ? ORDER BY order_date DESC");
$orderStmt->execute([$guest_id]);
$orders = $orderStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Orders</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .orders-container {
            max-width: 900px;
            margin: 40px auto;
            font-family: Arial, sans-serif;
        }

        .order-card {
            border: 1px solid #ccc;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 12px;
            background-color: #f9f9f9;
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            font-weight: bold;
            margin-bottom: 15px;
            color: #333;
        }

        .item {
            display: flex;
            align-items: center;
            gap: 15px;
            border-top: 1px solid #ddd;
            padding: 15px 0;
        }

        .item img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
        }

        .item-details {
            flex-grow: 1;
        }

        .item-details strong {
            display: block;
            font-size: 16px;
        }

        .total {
            text-align: right;
            font-weight: bold;
            font-size: 16px;
            color: #444;
            margin-top: 10px;
        }

        .no-orders {
            text-align: center;
            font-size: 18px;
            color: #666;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
<div class="orders-container">
    <h2>My Purchase History</h2>

    <?php if (count($orders) === 0): ?>
        <p class="no-orders">You have not placed any orders yet.</p>
    <?php else: ?>
        <?php foreach ($orders as $order): ?>
            <div class="order-card">
                <div class="order-header">
                    <div>
                        <div>Order ID: <?= $order['order_id'] ?></div>
                        <div>Payment Method: <?= htmlspecialchars($order['payment_method']) ?></div>
                    </div>
                    <span><?= date('d M Y, H:i', strtotime($order['order_date'])) ?></span>
                </div>

                <?php
                $itemStmt = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
                $itemStmt->execute([$order['order_id']]);
                $items = $itemStmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($items as $item):
                    // Try to find the product from all known product tables
                    $product = null;
                    $tables = ['flowers', 'photos', 'bears']; // Add your actual table names here

                    foreach ($tables as $table) {
                        $stmt = $conn->prepare("SELECT * FROM `$table` WHERE id = ?");
                        $stmt->execute([$item['product_id']]);
                        $product = $stmt->fetch(PDO::FETCH_ASSOC);
                        if ($product) break;
                    }

                    if ($product):
                ?>
                    <div class="item">
                        <img src="<?= $product['img_url'] ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                        <div class="item-details">
                            <strong><?= htmlspecialchars($product['name']) ?></strong>
                            <span>RM <?= $item['unit_price'] ?> x <?= $item['quantity'] ?> = <strong>RM <?= $item['unit_price'] * $item['quantity'] ?></strong></span>
                        </div>
                    </div>
                <?php endif; endforeach; ?>

                <div class="total">Total Paid: RM <?= number_format($order['total_amount'], 2) ?></div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<?php include 'footer.php'; ?>
</body>
</html>
