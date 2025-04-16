<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Graduation Shop</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<h1>Graduation Products</h1>
<?php
$stmt = $conn->query("SELECT * FROM products");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "<div class='product'>
            <h3>{$row['name']}</h3>
            <p>RM {$row['price']}</p>
            <a href='cart.php?action=add&id={$row['id']}'>Add to Cart</a>
          </div>";
}
?>
<a href="cart.php">View Cart</a>
</body>
</html>
