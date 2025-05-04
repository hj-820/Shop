<?php
// 1) Include your DB connection
include 'db.php';

// 2) Ensure we have a guest_id cookie
if (!isset($_COOKIE['guest_id'])) {
    $guest_id = uniqid('guest_', true);
    setcookie('guest_id', $guest_id, time() + 86400 * 30, '/');
} else {
    $guest_id = $_COOKIE['guest_id'];
}

// 3) Check payments for this guest_id
$has_payment = false;
try {
    $stmt = $conn->prepare("
        SELECT 1 
          FROM payment 
         WHERE guest_id = :guest_id 
         LIMIT 1
    ");
    $stmt->execute([':guest_id' => $guest_id]);
    $has_payment = $stmt->fetchColumn() !== false;
} catch (PDOException $e) {
    // In production you might log this rather than echo
    echo "Database error: " . htmlspecialchars($e->getMessage());
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Graduation Shop</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="logo-bar">
            <h1>🎓 GradElite Graduation Shop</h1>
        </div>
        <div class="navbar">
            <nav class="nav-left">
                <a href="index.php">Home</a>
                <a href="products.php">Products</a>
                <a href="about.php">About Us</a>
            </nav>
            <nav class="nav-right">
                <a href="cart.php">View Cart</a>
                <?php if ($has_payment): ?>
                    <a href="orders.php">View Orders</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main>
        <!-- Your page content -->
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>
