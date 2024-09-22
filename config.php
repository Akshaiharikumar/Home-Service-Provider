<?php
$servername = "localhost:3307";
$username = "root"; // default username for MySQL
$password = ""; // default password for MySQL
$dbname = "home_services_db";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?> 

