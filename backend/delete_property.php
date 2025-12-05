<?php
require __DIR__ . "/../includes/auth_check.php";
require __DIR__ . "/../includes/db_connect.php";

$id = $_GET["id"];

$stmt = $conn->prepare("DELETE FROM properties WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: ../dashboard.php");
exit();
?>
