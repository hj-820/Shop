<?php
$host = getenv('DB_HOST');
var_dump($host);  // This should print the new RDS endpoint, not the old one
 $dbname = getenv('DB_NAME');
 $username = getenv('DB_USER');
 $password = getenv('DB_PASS');

// Force TCP/IP connection by adding host and port explicitly
$dsn = "mysql:host=$host;port=3306;dbname=$dbname";

try {
echo ('DB_HOST');
    $conn = new PDO($dsn, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Database connection failed: " . $e->getMessage();
}
?>
