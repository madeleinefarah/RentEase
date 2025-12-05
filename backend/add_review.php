<?php
require __DIR__ . "/../includes/auth_check.php";
require __DIR__ . "/../includes/db_connect.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../index.php");
    exit();
}

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

$user_id     = (int) $_SESSION["user_id"];
$property_id = isset($_POST["property_id"]) ? (int) $_POST["property_id"] : 0;
$rating      = isset($_POST["rating"]) ? (int) $_POST["rating"] : 0;
$comment     = trim($_POST["comment"] ?? "");

if ($property_id <= 0 || $rating < 1 || $rating > 5 || $comment === "") {
    header("Location: ../property.php?id=" . $property_id);
    exit();
}

$stmt = $conn->prepare("
    INSERT INTO reviews (property_id, user_id, rating, comment)
    VALUES (?, ?, ?, ?)
");
$stmt->bind_param("iiis", $property_id, $user_id, $rating, $comment);
$stmt->execute();
$stmt->close();

header("Location: ../property.php?id=" . $property_id);
exit();
