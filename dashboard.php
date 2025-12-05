<?php
require __DIR__ . "/includes/auth_check.php";
require __DIR__ . "/includes/db_connect.php";

$user_id = $_SESSION["user_id"];



// fetch the total listings
$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM properties WHERE owner_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$total_listings = $stmt->get_result()->fetch_assoc()["total"];

// fetch the total favorites
$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM favorites WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$total_favorites = $stmt->get_result()->fetch_assoc()["total"];


$booking_count = 0;

// Temporary listings count for the user
$stmt = $conn->prepare("SELECT * FROM properties WHERE owner_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$listings = $stmt->get_result();



$page_title = "Dashboard";
require __DIR__ . "/includes/header.php";
?>

<div class="dashboard-container">

    <h1 class="dashboard-title">Dashboard</h1>

    <div class="dashboard-stats">
        <div class="stat-box">
            <h3>Your Listings</h3>
            <p><?php echo $total_listings; ?></p>
        </div>

        <div class="stat-box">
            <h3>Your Favorites</h3>
            <p><?php echo $total_favorites; ?></p>
        </div>

        <div class="stat-box">
            <h3>Your Bookings</h3>
            <p><?php echo $booking_count; ?></p>
        </div>
    </div>


    <a class="add-listing-btn" href="/stayease/backend/add_property.php">+ Add New Listing</a>


    <div class="dashboard-section">
        <h2>Your Properties</h2>

        <?php if ($listings->num_rows > 0): ?>
        <table class="dashboard-table">

            <tr>
                <th>Image</th>
                <th>Title</th>
                <th>City</th>
                <th>Price</th>
                <th>Actions</th>
            </tr>

            <?php while ($row = $listings->fetch_assoc()): ?>
            <tr>
                <td>
                    <img src="/stayease/<?php echo $row['main_image']; ?>" alt="">
                </td>

                <td><?php echo htmlspecialchars($row['title']); ?></td>

                <td><?php echo htmlspecialchars($row['city']); ?></td>

                <td>$<?php echo number_format($row['price'], 2); ?></td>

                <td>
                    <a class="table-btn view-btn" 
                       href="/stayease/property.php?id=<?php echo $row['id']; ?>">View</a>

                    <a class="table-btn edit-btn" 
                       href="/stayease/backend/edit_property.php?id=<?php echo $row['id']; ?>">Edit</a>

                    <a class="table-btn delete-btn" 
                       href="/stayease/backend/delete_property.php?id=<?php echo $row['id']; ?>"
                       onclick="return confirm('Are you sure you want to delete this property?');">
                        Delete
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>

        </table>

        <?php else: ?>

        <p>You have no properties yet.</p>

        <?php endif; ?>

    </div>

</div>

<?php require __DIR__ . "/includes/footer.php"; ?>
