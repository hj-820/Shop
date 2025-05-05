<?php
$host = var_dump(getenv('DB_HOST'));
 $dbname = var_dump(getenv('DB_NAME'));
 $username = var_dump(getenv('DB_USER'));
 $password = var_dump(getenv('DB_PASS'));

// Force TCP/IP connection by adding host and port explicitly
$dsn = "mysql:host=$host;port=3306;dbname=$dbname";

try {
echo $host;
    $conn = new PDO($dsn, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Database connection failed: " . $e->getMessage();
}
?>
