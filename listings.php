<?php
$page_title = "Browse Listings";

require __DIR__ . "/includes/header.php";
require __DIR__ . "/includes/db_connect.php";


//these are filters

$type  = isset($_GET['type']) ? $_GET['type'] : 'all';
$city  = isset($_GET['city']) ? trim($_GET['city']) : '';

$min_price = isset($_GET['min_price']) ? (int)$_GET['min_price'] : 0;
$max_price = isset($_GET['max_price']) ? (int)$_GET['max_price'] : 0;

$wifi   = isset($_GET['wifi']) ? 1 : 0;
$parking = isset($_GET['parking']) ? 1 : 0;
$ac     = isset($_GET['ac']) ? 1 : 0;
$pool   = isset($_GET['pool']) ? 1 : 0;

$guests = isset($_GET['guests']) ? (int)$_GET['guests'] : 0;

$pet_friendly = isset($_GET['pet_friendly']) ? 1 : 0;
$heating      = isset($_GET['heating']) ? 1 : 0;
$kitchen      = isset($_GET['kitchen']) ? 1 : 0;
$tv           = isset($_GET['tv']) ? 1 : 0;



$where = "WHERE status = 'approved'";

if ($type !== 'all') {
    $where .= " AND type = '" . $conn->real_escape_string($type) . "'";
}

if ($city !== '') {
    $where .= " AND city LIKE '%" . $conn->real_escape_string($city) . "%'";
}

if ($min_price > 0) {
    $where .= " AND price >= " . $min_price;
}

if ($max_price > 0 && $max_price >= $min_price) {
    $where .= " AND price <= " . $max_price;
}

if ($wifi)         $where .= " AND has_wifi = 1";
if ($parking)      $where .= " AND has_parking = 1";
if ($ac)           $where .= " AND has_ac = 1";
if ($pool)         $where .= " AND has_pool = 1";
if ($pet_friendly) $where .= " AND has_pet_friendly = 1";
if ($heating)      $where .= " AND has_heating = 1";
if ($kitchen)      $where .= " AND has_kitchen = 1";
if ($tv)           $where .= " AND has_tv = 1";

if ($guests > 0) {
    $where .= " AND max_guests = $guests";
}



$query  = "SELECT * FROM properties $where ORDER BY created_at DESC";
$result = $conn->query($query);
?>

<h1>Browse Properties</h1>

<div class="filter-bar">

    <a href="/stayease/listings.php?type=all"
       class="filter-btn <?php echo ($type=='all' ? 'active' : ''); ?>">
       ✨ All listings
    </a>

    <a href="/stayease/listings.php?type=guesthouse"
       class="filter-btn <?php echo ($type=='guesthouse' ? 'active' : ''); ?>">
       🏠 Guesthouses
    </a>

    <a href="/stayease/listings.php?type=apartment"
       class="filter-btn <?php echo ($type=='apartment' ? 'active' : ''); ?>">
       🏢 Apartments
    </a>

    <a href="/stayease/listings.php?type=camping"
       class="filter-btn <?php echo ($type=='camping' ? 'active' : ''); ?>">
       🏕 Camping
    </a>
</div>


<form method="GET" class="filters-form">
    <input type="hidden" name="type" value="<?php echo htmlspecialchars($type); ?>">

    <div class="filters-container">

        
        <div class="filter-section">
            <div class="filter-header"><h3>City</h3></div>
            <input type="text" name="city" class="city-input"
                   value="<?php echo htmlspecialchars($city); ?>"
                   placeholder="e.g. Beirut">
        </div>

        
        <div class="filter-section">
            <div class="filter-header"><h3>Price range</h3></div>

            <div class="price-slider">
                <input type="range" min="10" max="1000" name="min_price" id="minPrice"
                       value="<?php echo $min_price > 0 ? $min_price : 50; ?>">

                <input type="range" min="10" max="1000" name="max_price" id="maxPrice"
                       value="<?php echo $max_price > 0 ? $max_price : 500; ?>">

                <div class="price-labels">
                    <span id="minPriceLabel">$<?php echo $min_price ?: 50; ?></span>
                    <span id="maxPriceLabel">$<?php echo $max_price ?: 500; ?></span>
                </div>
            </div>
        </div>

        
        <div class="filter-section">
            <div class="filter-header"><h3>Guests</h3></div>
            <input type="number" min="1" max="20" name="guests" class="city-input"
                   value="<?php echo $guests ?: ''; ?>"
                   placeholder="Any number of guests">
        </div>

        
        <div class="filter-section">
            <div class="filter-header"><h3>Amenities</h3></div>

            <div class="filter-options">

                <label class="filter-pill <?php if($wifi) echo 'active'; ?>">
                    <input type="checkbox" name="wifi" <?php if($wifi) echo 'checked'; ?>>
                    📶 Wifi
                </label>

                <label class="filter-pill <?php if($ac) echo 'active'; ?>">
                    <input type="checkbox" name="ac" <?php if($ac) echo 'checked'; ?>>
                    ❄ Air conditioning
                </label>

                <label class="filter-pill <?php if($parking) echo 'active'; ?>">
                    <input type="checkbox" name="parking" <?php if($parking) echo 'checked'; ?>>
                    🅿 Parking
                </label>

                <label class="filter-pill <?php if($pool) echo 'active'; ?>">
                    <input type="checkbox" name="pool" <?php if($pool) echo 'checked'; ?>>
                    🏊 Pool
                </label>

                <label class="filter-pill <?php if($pet_friendly) echo 'active'; ?>">
                    <input type="checkbox" name="pet_friendly" <?php if($pet_friendly) echo 'checked'; ?>>
                    🐶 Pet friendly
                </label>

                <label class="filter-pill <?php if($heating) echo 'active'; ?>">
                    <input type="checkbox" name="heating" <?php if($heating) echo 'checked'; ?>>
                    🔥 Heating
                </label>

                <label class="filter-pill <?php if($kitchen) echo 'active'; ?>">
                    <input type="checkbox" name="kitchen" <?php if($kitchen) echo 'checked'; ?>>
                    🍳 Kitchen
                </label>

                <label class="filter-pill <?php if($tv) echo 'active'; ?>">
                    <input type="checkbox" name="tv" <?php if($tv) echo 'checked'; ?>>
                    📺 TV
                </label>

            </div>
        </div>

        <div class="filter-actions">
            <a href="/stayease/listings.php" class="clear-btn">Clear all</a>
            <button type="submit" class="apply-btn">Show results</button>
        </div>

    </div>
</form>


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
                        $fav = $conn->query("SELECT id FROM favorites WHERE user_id=$u AND property_id=$p");
                        echo ($fav->num_rows > 0) ? "♥" : "♡";
                    } else {
                        echo "♡";
                    }
                ?>
            </div>
        </div>

        <h3><?php echo htmlspecialchars($row['title']); ?></h3>

        <p class="location">
            <?php echo htmlspecialchars($row['city']); ?> · <?php echo ucfirst($row['type']); ?>
        </p>

        <p class="details">
            <?php echo $row['max_guests']; ?> guests
        </p>

        <p class="price">
            $<?php echo number_format($row['price'], 2); ?> / night
        </p>

        <a class="book-btn" href="/stayease/property.php?id=<?php echo $row['id']; ?>">
            Book now
        </a>

    </div>
<?php endwhile; ?>

</div>

<?php require __DIR__ . "/includes/footer.php"; ?>
