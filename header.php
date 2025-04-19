<?php
if (!isset($_COOKIE['guest_id'])) {
    $guest_id = uniqid('guest_', true);
    setcookie('guest_id', $guest_id, time() + (86400 * 30), "/"); // 30 days
} else {
    $guest_id = $_COOKIE['guest_id'];
}
?>


<!DOCTYPE html>
<html>
<head>
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
            </nav>
        </div>
    </header>

    <main>
    </main>
</body>
</html>
