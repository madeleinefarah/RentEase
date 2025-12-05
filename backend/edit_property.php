<?php
require __DIR__ . "/../includes/auth_check.php";
require __DIR__ . "/../includes/db_connect.php";


if (!isset($_GET['id'])) {
    die("Property not found.");
}

$property_id = (int) $_GET['id'];
$owner_id    = (int) $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT * FROM properties
    WHERE id = ? AND owner_id = ?
");
$stmt->bind_param("ii", $property_id, $owner_id);
$stmt->execute();
$property = $stmt->get_result()->fetch_assoc();

if (!$property) {
    die("You do not have permission to edit this property, or it does not exist.");
}

$errors = [];


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title       = trim($_POST["title"]);
    $description = trim($_POST["description"]);
    $price       = trim($_POST["price"]);
    $city        = trim($_POST["city"]);
    $address     = trim($_POST["address"]);
    $type        = trim($_POST["type"]);
    $max_guests  = trim($_POST["max_guests"]);


    if ($title === "" || $price === "" || $city === "" || $type === "") {
        $errors[] = "Please fill in all required fields marked with *.";
    }

    if ($price !== "" && !is_numeric($price)) {
        $errors[] = "Price must be a number.";
    }

    if ($max_guests !== "" && !ctype_digit($max_guests)) {
        $errors[] = "Max guests must be an integer.";
    }

    $newImagePath = $property['main_image'];

    if (isset($_FILES["main_image"]) && $_FILES["main_image"]["error"] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES["main_image"];

        if ($file["error"] !== UPLOAD_ERR_OK) {
            $errors[] = "There was an error uploading the image.";
        } else {
            $allowedTypes = ["image/jpeg", "image/png", "image/gif", "image/webp"];
            if (!in_array($file["type"], $allowedTypes)) {
                $errors[] = "Only JPG, PNG, GIF or WEBP images are allowed.";
            }

            if ($file["size"] > 2 * 1024 * 1024) {
                $errors[] = "Image must be smaller than 2MB.";
            }
        }

        if (empty($errors)) {
            $uploadDir = __DIR__ . "/../assets/uploads/";
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $ext      = pathinfo($file["name"], PATHINFO_EXTENSION);
            $newName  = "property_" . time() . "_" . mt_rand(1000, 9999) . "." . $ext;
            $destPath = $uploadDir . $newName;

            $dbImagePath = "assets/uploads/" . $newName;

            if (!move_uploaded_file($file["tmp_name"], $destPath)) {
                $errors[] = "Could not save uploaded image. Please try again.";
            } else {
                $newImagePath = $dbImagePath;
            }
        }
    }


    if (empty($errors)) {
        $price_val      = (float) $price;
        $max_guests_val = ($max_guests === "") ? null : (int) $max_guests;

        $update = $conn->prepare("
            UPDATE properties
            SET title = ?, description = ?, price = ?, city = ?, address = ?, type = ?, max_guests = ?, main_image = ?
            WHERE id = ? AND owner_id = ?
        ");

        $update->bind_param(
            "ssdsssissi",
            $title,
            $description,
            $price_val,
            $city,
            $address,
            $type,
            $max_guests_val,
            $newImagePath,
            $property_id,
            $owner_id
        );

        $update->execute();

        echo "<script>
            alert('Your property has been updated successfully!');
            window.location.href = '../dashboard.php';
        </script>";
        exit;
    } else {
        $property['title']       = $title;
        $property['description'] = $description;
        $property['price']       = $price;
        $property['city']        = $city;
        $property['address']     = $address;
        $property['type']        = $type;
        $property['max_guests']  = $max_guests;
        $property['main_image']  = $newImagePath;
    }
}

$page_title = "Edit Property";
require __DIR__ . "/../includes/header.php";
?>

<div class="add-property-container">
    <div class="add-property-box">
        <h1>Edit Property</h1>
        <p class="add-property-subtitle">
            Update details for your listing.
        </p>

        <?php if (!empty($errors)): ?>
            <div class="form-error-box">
                <?php foreach ($errors as $err): ?>
                    <p><?php echo htmlspecialchars($err); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="add-property-form" enctype="multipart/form-data">
            <label>Title *</label>
            <input type="text" name="title"
                   value="<?php echo htmlspecialchars($property['title']); ?>"
                   required>

            <label>Description</label>
            <textarea name="description" rows="4"
                      placeholder="Describe your property, neighborhood, and any special details."><?php
                echo htmlspecialchars($property['description']);
            ?></textarea>

            <div class="form-row">
                <div class="field">
                    <label>Price per night (USD) *</label>
                    <input type="number" step="0.01" min="0" name="price"
                           value="<?php echo htmlspecialchars($property['price']); ?>"
                           required>
                </div>
                <div class="field">
                    <label>Max Guests</label>
                    <input type="number" min="1" name="max_guests"
                           value="<?php echo htmlspecialchars($property['max_guests']); ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="field">
                    <label>City *</label>
                    <input type="text" name="city"
                           value="<?php echo htmlspecialchars($property['city']); ?>"
                           required>
                </div>
                <div class="field">
                    <label>Type *</label>
                    <select name="type" required>
                        <?php
                        $currentType = $property['type'];
                        $types = [
                            ''           => 'Select type',
                            'apartment'  => 'Apartment',
                            'guesthouse' => 'Guesthouse',
                            'camping'    => 'Camping',
                            'house'      => 'House',
                        ];
                        foreach ($types as $value => $label) {
                            $selected = ($currentType === $value) ? 'selected' : '';
                            echo "<option value=\"{$value}\" {$selected}>{$label}</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>

            <label>Address</label>
            <input type="text" name="address"
                   value="<?php echo htmlspecialchars($property['address']); ?>">

            <label>Current Image</label>
            <div style="margin-bottom: 10px;">
                <img src="<?php echo htmlspecialchars($property['main_image']); ?>"
                     alt="Current image"
                     style="max-width: 200px; border-radius: 10px;">
            </div>

            <label>Change Image (optional)</label>
            <div class="file-upload-wrapper">
                <label class="custom-file-upload">
                    <input type="file" name="main_image" id="mainImageInput" accept="image/*">
                    Upload New Image
                </label>
                <span id="fileNameDisplay">No file chosen</span>
            </div>

            <button type="submit" class="save-property-btn">
                Save Changes
            </button>
        </form>
    </div>
</div>

<?php require __DIR__ . "/../includes/footer.php"; ?>
