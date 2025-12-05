<?php
// this starts the session if it is not strarted yet
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($page_title)) {
    $page_title = "Rental Platform";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?php echo htmlspecialchars($page_title); ?></title>

    
    <link rel="stylesheet" href="/stayease/assets/css/style.css">
    <script src="/stayease/assets/js/app.js" defer></script>
</head>

<body data-logged-in="<?php echo isset($_SESSION['user_id']) ? '1' : '0'; ?>">

<header>
    <?php require __DIR__ . "/navbar.php"; ?>
</header>

<main>
