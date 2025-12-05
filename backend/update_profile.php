<?php
require __DIR__ . "/../includes/auth_check.php";
require __DIR__ . "/../includes/db_connect.php";

$user_id = $_SESSION['user_id'];

$name  = trim($_POST['name']);
$email = trim($_POST['email']);
$phone = trim($_POST['phone']);
$bio   = trim($_POST['bio']);

if (empty($name) || empty($email)) {
    die("Name and email are required.");
}

$stmt = $conn->prepare("
    UPDATE users 
    SET name = ?, email = ?, phone = ?, bio = ?
    WHERE id = ?
");

$stmt->bind_param("ssssi", $name, $email, $phone, $bio, $user_id);

if ($stmt->execute()) {
    $_SESSION['user_name'] = $name;

    echo "
        <script>
        alert('Your profile has been updated successfully!');
        window.location.href = '../profile.php';
        </script>
    ";
} else {
    echo "Error updating profile: " . $conn->error;
}
