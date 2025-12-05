<?php
session_start();
require __DIR__ . "/../includes/db_connect.php";

if (!isset($_SESSION["user_id"])) {
    echo "LOGIN_REQUIRED";
    exit;
}

$user_id = $_SESSION["user_id"];

if (!isset($_GET["id"])) {
    echo "ERROR";
    exit;
}

$property_id = intval($_GET["id"]);

$stmt = $conn->prepare("SELECT id FROM favorites WHERE user_id = ? AND property_id = ?");
$stmt->bind_param("ii", $user_id, $property_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $stmt = $conn->prepare("DELETE FROM favorites WHERE user_id = ? AND property_id = ?");
    $stmt->bind_param("ii", $user_id, $property_id);
    $stmt->execute();
    echo "REMOVED";
} else {
    $stmt = $conn->prepare("INSERT INTO favorites (user_id, property_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $user_id, $property_id);
    $stmt->execute();
    echo "ADDED";
}
