<?php
$page_title = "Home";

require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/db_connect.php';

$filter = isset($_GET['type']) ? $_GET['type'] : 'all';

$whereSQL = "WHERE status = 'approved'";

if ($filter !== 'all') {
    $filterSafe = $conn->real_escape_string($filter);
    $whereSQL .= " AND type = '$filterSafe'";
}

$query = "SELECT * FROM properties $whereSQL ORDER BY created_at DESC LIMIT 50";
$result = $conn->query($query);
?>

<h1>Latest Listings</h1>

<div class="filter-bar">
    <a href="/stayease/index.php?type=all" 
        class="filter-btn <?php echo ($filter=='all' ? 'active' : ''); ?>">
        ✨ All listings
    </a>

    <a href="/stayease/index.php?type=guesthouse" 
        class="filter-btn <?php echo ($filter=='guesthouse' ? 'active' : ''); ?>">
        🏠 Guesthouses
    </a>

    <a href="/stayease/index.php?type=apartment" 
        class="filter-btn <?php echo ($filter=='apartment' ? 'active' : ''); ?>">
        🏢 Apartments
    </a>

    <a href="/stayease/index.php?type=camping" 
        class="filter-btn <?php echo ($filter=='camping' ? 'active' : ''); ?>">
        🏕 Camping
    </a>
</div>


<div class="cards">
<?php while ($row = $result->fetch_assoc()): ?>
    <div class="card">

        <div class="card-img-wrap">
            <img src="/stayease/<?php echo $row['main_image']; ?>" alt="Property Image">

            <div class="heart-btn" data-id="<?php echo $row['id']; ?>">
                <?php
                    if (isset($_SESSION['user_id'])) {
                        $u = $_SESSION['user_id'];
                        $p = $row['id'];
                        $fav_check = $conn->query("SELECT id FROM favorites WHERE user_id=$u AND property_id=$p");
                        echo ($fav_check->num_rows > 0) ? "♥" : "♡";
                    } else {
                        echo "♡";
                    }
                ?>
            </div>
        </div>

        <h3><?php echo htmlspecialchars($row['title']); ?></h3>

        <p class="location">
            <?php echo htmlspecialchars($row['city']); ?> · 
            <?php echo htmlspecialchars($row['type']); ?>
        </p>

        <p class="details">
            <?php echo htmlspecialchars($row['max_guests']); ?> guests · 
            <?php echo htmlspecialchars($row['type']); ?>
        </p>

        <p class="price">
            $<?php echo number_format($row['price'], 2); ?> / night
        </p>

        <a class="view-button"
            href="/stayease/property.php?id=<?php echo $row['id']; ?>">
            View
        </a>


    </div>
<?php endwhile; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
