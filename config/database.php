
<?php

// Database Configuration

$host = "localhost";
$dbname = "placement_portal";
$username = "root";
$password = "";

try {

    // Create PDO connection

    $conn = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );

    // Enable error reporting

    $conn->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

    // Fetch database records as associative arrays

    $conn->setAttribute(
        PDO::ATTR_DEFAULT_FETCH_MODE,
        PDO::FETCH_ASSOC
    );

} catch (PDOException $e) {

    // Display connection error

    die("Database Connection Failed: " . $e->getMessage());

}

?>
