<?php
require __DIR__ . "/includes/db_connect.php";
require __DIR__ . "/includes/auth_check.php";

$page_title = "Your Favorites";
require __DIR__ . "/includes/header.php";

$user_id = $_SESSION["user_id"];

// these are prepared statement for safety
$stmt = $conn->prepare("
    SELECT p.*
    FROM favorites f
    JOIN properties p ON f.property_id = p.id
    WHERE f.user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<h1>Your Favorites</h1>

<div class="cards">

<?php while ($row = $result->fetch_assoc()): ?>
    <div class="card">

        <div class="card-img-wrap">
            <img src="/stayease/<?php echo $row['main_image']; ?>" alt="Property image">

            <div class="heart-btn" 
                 data-id="<?php echo $row['id']; ?>">
                 ♥
            </div>
        </div>

        <h3><?php echo htmlspecialchars($row['title']); ?></h3>

        <p class="location">
            <?php echo htmlspecialchars($row['city']); ?> · 
            <?php echo htmlspecialchars($row['type']); ?>
        </p>

        <p class="price">
            $<?php echo number_format($row['price'], 2); ?> / night
        </p>

        <a class="book-btn" 
            href="/stayease/property.php?id=<?php echo $row['id']; ?>">
            View
        </a>

    </div>
<?php endwhile; ?>

</div>

<?php require __DIR__ . "/includes/footer.php"; ?>
