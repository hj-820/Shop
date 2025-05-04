<?php
$host = 'shopdb.csbwsu7vetpu.us-east-1.rds.amazonaws.com';
$dbname = 'shop';
$username = getenv('DB_USER');
$password = getenv('DB_PASS');

// Force TCP/IP connection by adding host and port explicitly
$dsn = "mysql:host=$host;port=3306;dbname=$dbname";

try {
    $conn = new PDO($dsn, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Database connection failed: " . $e->getMessage();
}
?>
