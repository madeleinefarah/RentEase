<?php
require __DIR__ . "/includes/auth_check.php";
require __DIR__ . "/includes/db_connect.php";

$user_id = $_SESSION['user_id'];

/* to cancel the booking */
if (isset($_GET['cancel_id'])) {
    $cancel_id = (int) $_GET['cancel_id'];

    $stmt = $conn->prepare("DELETE FROM bookings WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $cancel_id, $user_id);
    $stmt->execute();

    header("Location: my_bookings.php");
    exit;
}

/* fetch the users bookings*/
$result = $conn->query("
    SELECT b.*,
           p.title,
           p.city,
           p.main_image,
           p.price
    FROM bookings b
    JOIN properties p ON b.property_id = p.id
    WHERE b.user_id = $user_id
    ORDER BY b.created_at DESC
");

$page_title = "My Bookings";
require __DIR__ . "/includes/header.php";
?>

<h1>My Bookings</h1>

<div class="cards">

<?php while ($row = $result->fetch_assoc()): ?>
    <div class="card booking-card">
        <img src="<?php echo $row['main_image']; ?>"
             style="width:100%; height:200px; object-fit:cover; border-radius:12px;">

        <h3><?php echo $row['title']; ?></h3>
        <p><?php echo $row['city']; ?></p>

        <p><strong>Check-in:</strong> <?php echo $row['start_date']; ?></p>
        <p><strong>Check-out:</strong> <?php echo $row['end_date']; ?></p>
        <p><strong>Total price:</strong> $<?php echo $row['total_price']; ?></p>
        <p><strong>Status:</strong> <?php echo ucfirst($row['status']); ?></p>

        <a href="my_bookings.php?cancel_id=<?php echo $row['id']; ?>"
           class="cancel-booking-btn"
           onclick="return confirm('Are you sure you want to cancel this booking?');">
            Cancel Booking
        </a>
    </div>
<?php endwhile; ?>

</div>

<?php require __DIR__ . "/includes/footer.php"; ?>
