<?php
require __DIR__ . "/../includes/auth_check.php";
require __DIR__ . "/../includes/db_connect.php";

$user_id = $_SESSION["user_id"];
$property_id = $_GET["id"];

$stmt = $conn->prepare("INSERT INTO favorites (user_id, property_id) VALUES (?, ?)");
$stmt->bind_param("ii", $user_id, $property_id);
$stmt->execute();

header("Location: ../property.php?id=" . $property_id);
exit();
?>
