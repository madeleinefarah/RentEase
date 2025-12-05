<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "rental_platform";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
