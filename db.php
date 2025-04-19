<?php
$host = "shop-db.csbwsu7vetpu.us-east-1.rds.amazonaws.com";
$dbname = "shop";
$username = "admin";
$password = "password";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Enable PDO error mode
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Database connection failed: " . $e->getMessage();
}
?>
