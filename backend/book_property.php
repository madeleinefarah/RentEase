<?php
session_start();
require __DIR__ . "/../includes/db_connect.php";

ini_set('display_errors', 1);
error_reporting(E_ALL);


function back_with_error($property_id, $message) {
    $_SESSION['booking_error'] = $message;
    header("Location: ../property.php?id=" . $property_id . "&open_booking=1");
    exit;
}

// the user must be logged in
if (!isset($_SESSION['user_id'])) {
    $pid = isset($_POST['property_id']) ? (int)$_POST['property_id'] : 0;
    back_with_error($pid, "You must be logged in to book a property.");
}

// he must be POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../index.php");
    exit;
}

$user_id     = $_SESSION['user_id'];
$property_id = isset($_POST['property_id']) ? (int)$_POST['property_id'] : 0;
$start_date  = $_POST['check_in']  ?? '';
$end_date    = $_POST['check_out'] ?? '';

if (!$property_id) {
    header("Location: ../index.php");
    exit;
}

// date validation
if (empty($start_date) || empty($end_date)) {
    back_with_error($property_id, "Please select valid dates.");
}

if ($start_date >= $end_date) {
    back_with_error($property_id, "Check-out date must be after check-in date.");
}

// this is for check overlapping bookings
$overlapStmt = $conn->prepare("
    SELECT COUNT(*) AS cnt
    FROM bookings
    WHERE property_id = ?
      AND status IN ('pending', 'confirmed')
      AND NOT (end_date <= ? OR start_date >= ?)
");
$overlapStmt->bind_param("iss", $property_id, $start_date, $end_date);
$overlapStmt->execute();
$overlapResult = $overlapStmt->get_result()->fetch_assoc();

if ($overlapResult['cnt'] > 0) {
    back_with_error($property_id, "These dates are already booked. Please choose other dates.");
}


$priceStmt = $conn->prepare("SELECT price FROM properties WHERE id = ?");
$priceStmt->bind_param("i", $property_id);
$priceStmt->execute();
$priceRow = $priceStmt->get_result()->fetch_assoc();

if (!$priceRow) {
    back_with_error($property_id, "Property not found.");
}

$pricePerNight = (float) $priceRow['price'];

// to calculate the price for the stays 
$nights = (strtotime($end_date) - strtotime($start_date)) / 86400;
if ($nights <= 0) {
    back_with_error($property_id, "Check-out date must be after check-in date.");
}

$total_price = $nights * $pricePerNight;

// Insert booking
$status = "pending";

$stmt = $conn->prepare("
    INSERT INTO bookings (property_id, user_id, start_date, end_date, total_price, status)
    VALUES (?, ?, ?, ?, ?, ?)
");
$stmt->bind_param(
    "iissds",
    $property_id,
    $user_id,
    $start_date,
    $end_date,
    $total_price,
    $status
);
$stmt->execute();


$_SESSION['booking_success'] =
    "Your booking has been created! Total price: $" . number_format($total_price, 2);

header("Location: ../my_bookings.php");
exit;
