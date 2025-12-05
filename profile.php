<?php
require "includes/auth_check.php";
require "includes/db_connect.php";

$user_id = $_SESSION['user_id'];

// it fetchs users data from db
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$name    = htmlspecialchars($user['name'] ?? '');
$email   = htmlspecialchars($user['email'] ?? '');
$phone   = htmlspecialchars($user['phone'] ?? '');
$bio     = htmlspecialchars($user['bio'] ?? '');
$created = date("F Y", strtotime($user['created_at']));

$profile_image = $user['profile_image'] ?? "assets/img/default_avatar.png";

require "includes/header.php";
?>

<div class="profile-container">

    <div class="profile-card">

        <h2><?php echo $name; ?></h2>
        <p class="profile-email"><?php echo $email; ?></p>

        <p class="profile-member">Member since: <strong><?php echo $created; ?></strong></p>

        <div class="profile-links">
            <a href="my_bookings.php">📅 My Bookings</a>
            <a href="dashboard.php">🏡 My Listings</a>
        </div>
    </div>

    <div class="profile-forms">

        <div class="profile-section">
            <h3>Update Profile</h3>

            <form action="backend/update_profile.php" method="POST">

                <label>Name</label>
                <input type="text" name="name" value="<?php echo $name; ?>" required>

                <label>Email</label>
                <input type="email" name="email" value="<?php echo $email; ?>" required>

                <label>Phone</label>
                <input type="text" name="phone" value="<?php echo $phone; ?>">

                <label>Bio</label>
                <textarea name="bio" rows="4"><?php echo $bio; ?></textarea>

                <button type="submit" class="save-btn">Save Changes</button>
            </form>
        </div>

        <div class="profile-section">
            <h3>Change Password</h3>

            <form action="backend/update_password.php" method="POST">

                <label>Current Password</label>
                <input type="password" name="current_password" required>

                <label>New Password</label>
                <input type="password" name="new_password" required>

                <label>Confirm New Password</label>
                <input type="password" name="confirm_password" required>

                <button type="submit" class="save-btn">Update Password</button>
            </form>
        </div>

    </div>

</div>

<?php require "includes/footer.php"; ?>
