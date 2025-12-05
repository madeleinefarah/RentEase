<?php
require __DIR__ . "/../includes/auth_check.php";
require __DIR__ . "/../includes/db_connect.php";

$user_id = $_SESSION["user_id"];
$property_id = $_GET["id"];

$stmt = $conn->prepare("DELETE FROM favorites WHERE user_id = ? AND property_id = ?");
$stmt->bind_param("ii", $user_id, $property_id);
$stmt->execute();

header("Location: ../favorites.php");
exit();
?>
