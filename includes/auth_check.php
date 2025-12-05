<?php
session_start();

if (!isset($_SESSION["user_id"])) {

    // Detect if current file is inside the backend 
    $is_backend = str_contains(__DIR__, "backend");

    if ($is_backend) {
        // redirect to root login page
        header("Location: ../login.php");
    } else {
        header("Location: login.php");
    }

    exit();
}
?>
