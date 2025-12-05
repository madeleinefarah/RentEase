<?php
require __DIR__ . "/../includes/auth_check.php";
require __DIR__ . "/../includes/db_connect.php";

$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $owner_id    = $_SESSION["user_id"];
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

    if (!isset($_FILES["main_image"]) || $_FILES["main_image"]["error"] === UPLOAD_ERR_NO_FILE) {
        $errors[] = "Please upload a property image.";
    } else {
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
    }



    if (empty($errors)) {

        $uploadDir = __DIR__ . "/../assets/uploads/";

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $ext       = pathinfo($file["name"], PATHINFO_EXTENSION);
        $newName   = "property_" . time() . "_" . mt_rand(1000, 9999) . "." . $ext;
        $destPath  = $uploadDir . $newName;  //assets/uploads/property_1234.jpg the style 

        if (!move_uploaded_file($file["tmp_name"], $destPath)) {
            $errors[] = "Could not save uploaded image. Please try again.";
        } else {
            //it inserts the property into DB with status pending
            $stmt = $conn->prepare("INSERT INTO properties
                (owner_id, title, description, price, city, address, type, max_guests, main_image, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );

            $price_val      = (float)$price;
            $max_guests_val = ($max_guests === "") ? null : (int)$max_guests;
            $status         = "pending";

            $stmt->bind_param(
                "issdsssiss",
                $owner_id,
                $title,
                $description,
                $price_val,
                $city,
                $address,
                $type,
                $max_guests_val,
                $destPath,
                $status
            );

            $stmt->execute();

            
            echo "<script>
                alert('Thank you! The StayEase community is reviewing your property.');
                window.location.href = '../dashboard.php';
            </script>";
            exit();
        }
    }
}

$page_title = "Add Property";
require __DIR__ . "/../includes/header.php";
?>

<div class="add-property-container">
    <div class="add-property-box">
        <h1>Add New Property</h1>
        <p class="add-property-subtitle">
            Create a new listing so guests can discover your place.
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
                   value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>"
                   required>


            <label>Description</label>
            <textarea name="description" rows="4"
                      placeholder="Describe your property, neighborhood, and any special details."><?php 
                echo htmlspecialchars($_POST['description'] ?? ''); 
            ?></textarea>

            <div class="form-row">
                <div class="field">
                    <label>Price per night (USD) *</label>
                    <input type="number" step="0.01" min="0" name="price"
                           value="<?php echo htmlspecialchars($_POST['price'] ?? ''); ?>"
                           required>
                </div>
                <div class="field">
                    <label>Max Guests</label>
                    <input type="number" min="1" name="max_guests"
                           value="<?php echo htmlspecialchars($_POST['max_guests'] ?? ''); ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="field">
                    <label>City *</label>
                    <input type="text" name="city"
                           value="<?php echo htmlspecialchars($_POST['city'] ?? ''); ?>"
                           required>
                </div>
                <div class="field">
                    <label>Type *</label>
                    <select name="type" required>
                        <?php
                        $currentType = $_POST['type'] ?? '';
                        $types = [
                            ''            => 'Select type',
                            'apartment'   => 'Apartment',
                            'guesthouse'  => 'Guesthouse',
                            'camping'     => 'Camping',
                            'house'       => 'House',
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
                   value="<?php echo htmlspecialchars($_POST['address'] ?? ''); ?>">


           <label>Property Image *</label>

<div class="file-upload-wrapper">
    <label class="custom-file-upload">
        <input type="file" name="main_image" id="mainImageInput" accept="image/*" required>
        Upload Image
    </label>
    <span id="fileNameDisplay">No file chosen</span>
</div>

            <button type="submit" class="save-property-btn">
                Save Property
            </button>
        </form>
    </div>
</div>

<?php require __DIR__ . "/../includes/footer.php"; ?>
