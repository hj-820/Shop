<?php include 'db.php'; ?>
<?php include 'header.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>GradElite Graduation Shop</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h1 style="margin-left: 70px;">Graduation Products</h1>

<h3 style="margin-left: 90px;">Graduation Flower Bouquet</h3>
<div class="product-container">
<?php
$stmt = $conn->query("SELECT * FROM flowers");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "<div class='product-box'>
            <img src='{$row['img_url']}' alt='{$row['name']}' class='product-img'>
            <h3>{$row['name']}</h3>
            <p class='price'>RM {$row['price']}</p>
            <p class='desc'>{$row['description']}</p>
            <a class='add-to-cart' href='cart.php?action=add&id={$row['id']}'>Add to Cart</a>
          </div>";
}
?>
</div>

<h3 style="margin-left: 90px;">Graduation Photo Package</h3>
<div class="product-container">
<?php
$stmt = $conn->query("SELECT * FROM photos");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "<div class='product-box'>
            <img src='{$row['img_url']}' alt='{$row['name']}' class='product-img'>
            <h3>{$row['name']}</h3>
            <p class='price'>RM {$row['price']}</p>
            <ul class='desc'>";
            
            foreach (explode("\n", $row['description']) as $line) {
                echo "<li>" . htmlspecialchars(trim($line)) . "</li>";
            }

    echo "</ul>
            <a class='add-to-cart' href='cart.php?action=add&id={$row['id']}'>Add to Cart</a>
          </div>";
}
?>
</div>

    <h3 style="margin-left: 90px;">Graduation Bears</h3>
<div class="product-container">
<?php
$stmt = $conn->query("SELECT * FROM bears");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "<div class='product-box'>
            <img src='{$row['img_url']}' alt='{$row['name']}' class='product-img'>
            <h3>{$row['name']}</h3>
            <p class='price'>RM {$row['price']}</p>
            <p class='desc'>{$row['description']}</p>
            <a class='add-to-cart' href='cart.php?action=add&id={$row['id']}'>Add to Cart</a>
          </div>";
}
?>
</div>
<?php include 'footer.php'; ?>
</body>
</html>
