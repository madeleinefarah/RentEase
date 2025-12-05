<?php
$is_logged_in = isset($_SESSION["user_id"]);
?>

<nav class="navbar">
    <div class="nav-left">
        <a href="/stayease/index.php" class="nav-logo">RentEase</a>
        <a href="/stayease/listings.php" class="nav-link">Browse</a>
    </div>

    <div class="nav-right">
        <?php if ($is_logged_in): ?>
            <a href="/stayease/dashboard.php" class="nav-link">Dashboard</a>
            <a href="/stayease/favorites.php" class="nav-link">Favorites</a>
            <a href="/stayease/profile.php" class="nav-link">Profile</a>
            <a href="/stayease/logout.php" class="nav-btn logout-btn">Logout</a>
        <?php else: ?>
            <a href="/stayease/login.php" class="nav-btn login-btn">Login</a>
            <a href="/stayease/register.php" class="nav-btn register-btn">Sign Up</a>
        <?php endif; ?>
    </div>
</nav>
