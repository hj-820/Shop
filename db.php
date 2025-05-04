<?php
$host = getenv('DB_HOST');
$dbname = getenv('DB_NAME');
$username = getenv('DB_USER');
$password = getenv('DB_PASS');

// Force TCP/IP connection by adding host and port explicitly
$dsn = "mysql:host=$host;port=3306;dbname=$dbname";

try {
    // Create a PDO connection
    $conn = new PDO($dsn, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Query to fetch table names
    $query = "SHOW TABLES";
    $stmt = $conn->query($query);

    // Check if any tables exist
    $tables = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($tables) > 0) {
        echo "Tables in database '$dbname':<br>";
        foreach ($tables as $table) {
            echo $table['Tables_in_' . $dbname] . "<br>";
        }
    } else {
        echo "No tables found in database '$dbname'.";
    }
    
} catch(PDOException $e) {
    echo "Database connection failed: " . $e->getMessage();
}
?>
