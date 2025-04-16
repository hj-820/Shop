<?php
$host = "your-rds-endpoint.amazonaws.com";
$dbname = "ecommerce";
$username = "admin";
$password = "yourpassword";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Enable PDO error mode
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Database connection failed: " . $e->getMessage();
}
?>
