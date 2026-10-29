
<?php
session_start();

include "includes/db.php";

/* CHECK LOGIN */
if (!isset($_SESSION['user_id']) && !isset($_SESSION['admin'])) {
    header("Location: login_options.php");
    exit();
}

/* GET PROPERTY ID */
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    die("Invalid property ID.");
}

/* GET PROPERTY */
$stmt = mysqli_prepare($conn, "SELECT * FROM properties WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$property = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$property) {
    die("Property not found.");
}

/* CHECK OWNERSHIP */
if (!isset($_SESSION['admin'])) {
    if (
        !isset($_SESSION['user_id']) ||
        (int)$property['user_id'] !== (int)$_SESSION['user_id']
    ) {
        die("You are not authorized to edit this property.");
    }
}

/* UPDATE PROPERTY */
if (isset($_POST['update_property'])) {

    $title = trim($_POST['title'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $bedrooms = (int)($_POST['bedrooms'] ?? 0);
    $bathrooms = (int)($_POST['bathrooms'] ?? 0);
    $area = (float)($_POST['area'] ?? 0);
    $listing_type = $_POST['listing_type'] ?? '';
    $description = trim($_POST['description'] ?? '');

    // Keep the existing image if no new image is selected.
    $image = $property['image'];

    /* HANDLE NEW IMAGE UPLOAD */
    if (
        isset($_FILES['image']) &&
        $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE
    ) {
        if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            die("Image upload failed. Check the image size and try again.");
        }

        // Limit upload size to 5 MB.
        if ($_FILES['image']['size'] > 5 * 1024 * 1024) {
            die("Image is too large. Please select an image under 5 MB.");
        }

        $tmpFile = $_FILES['image']['tmp_name'];

        // Verify that the uploaded file is a real image.
        $imageInfo = getimagesize($tmpFile);

        if ($imageInfo === false) {
            die("The selected file is not a valid image.");
        }

        $allowedTypes = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp'
        ];

        $mimeType = $imageInfo['mime'];

        if (!isset($allowedTypes[$mimeType])) {
            die("Only JPG, PNG and WEBP images are allowed.");
        }

        // Images are stored in the project's images folder.
        $imageFolder = __DIR__ . DIRECTORY_SEPARATOR . 'images'
            . DIRECTORY_SEPARATOR;

        if (!is_dir($imageFolder)) {
            die("The images folder does not exist.");
        }

        if (!is_writable($imageFolder)) {
            die("The images folder is not writable. Check folder permissions.");
        }

        // Generate a unique filename to avoid overwriting other images.
        $newFileName = uniqid('property_', true)
            . '.' . $allowedTypes[$mimeType];

        $destination = $imageFolder . $newFileName;

        if (!move_uploaded_file($tmpFile, $destination)) {
            die("Unable to save the uploaded image. Please try again.");
        }

        $image = $newFileName;
    }

    /* UPDATE DATABASE */
    if (isset($_SESSION['admin'])) {

        // Admin edits do not change the approval status.
        $update_stmt = mysqli_prepare(
            $conn,
            "UPDATE properties
             SET title = ?,
                 location = ?,
                 price = ?,
                 bedrooms = ?,
                 bathrooms = ?,
                 area = ?,
                 listing_type = ?,
                 image = ?,
                 description = ?
             WHERE id = ?"
        );

        mysqli_stmt_bind_param(
            $update_stmt,
            "ssdiidsssi",
            $title,
            $location,
            $price,
            $bedrooms,
            $bathrooms,
            $area,
            $listing_type,
            $image,
            $description,
            $id
        );

    } else {

        // User edits require admin approval again.
        $status = "Pending";

        $update_stmt = mysqli_prepare(
            $conn,
            "UPDATE properties
             SET title = ?,
                 location = ?,
                 price = ?,
                 bedrooms = ?,
                 bathrooms = ?,
                 area = ?,
                 listing_type = ?,
                 image = ?,
                 description = ?,
                 status = ?
             WHERE id = ?"
        );

        mysqli_stmt_bind_param(
            $update_stmt,
            "ssdiidssssi",
            $title,
            $location,
            $price,
            $bedrooms,
            $bathrooms,
            $area,
            $listing_type,
            $image,
            $description,
            $status,
            $id
        );
    }

    if (!mysqli_stmt_execute($update_stmt)) {
        mysqli_stmt_close($update_stmt);
        die("Unable to update the property. Please try again.");
    }

    mysqli_stmt_close($update_stmt);

    $redirect = isset($_SESSION['admin'])
        ? "manage_properties.php"
        : "my_properties.php";

    echo "<script>
        alert('Property updated successfully!');
        window.location.href = " . json_encode($redirect) . ";
    </script>";

    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Edit Property - HomeFind</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background: #f5f7fb;
            color: #222;
        }

        .header {
            background: white;
            padding: 20px 8%;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #2563eb;
        }

        .container {
            width: 90%;
            max-width: 700px;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }

        h1 {
            margin-top: 0;
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-top: 15px;
            margin-bottom: 6px;
            font-weight: bold;
        }

        input, select, textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 15px;
            background: white;
        }

        input[type="file"] {
            padding: 10px;
            cursor: pointer;
        }

        textarea {
            resize: vertical;
        }

        .current-image {
            background: #f5f7fb;
            padding: 12px;
            border-radius: 6px;
            overflow-wrap: anywhere;
        }

        .hint {
            font-size: 13px;
            color: #666;
            margin-top: 6px;
        }

        button {
            margin-top: 20px;
            width: 100%;
            padding: 13px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
        }

        button:hover {
            background: #1d4ed8;
        }

        .back {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #2563eb;
            font-weight: bold;
        }
    </style>
</head>

<body>

<div class="header">
    <div class="logo">🏠 HomeFind</div>
</div>

<div class="container">

    <h1>✏️ Edit Property</h1>

    <form method="POST" enctype="multipart/form-data">

        <label for="title">Property Title</label>
        <input
            type="text"
            id="title"
            name="title"
            value="<?php echo htmlspecialchars($property['title']); ?>"
            required
        >

        <label for="location">Location</label>
        <input
            type="text"
            id="location"
            name="location"
            value="<?php echo htmlspecialchars($property['location']); ?>"
            required
        >

        <label for="price">Price</label>
        <input
            type="number"
            id="price"
            name="price"
            min="0"
            step="any"
            value="<?php echo htmlspecialchars($property['price']); ?>"
            required
        >

        <label for="bedrooms">Bedrooms</label>
        <input
            type="number"
            id="bedrooms"
            name="bedrooms"
            min="0"
            value="<?php echo htmlspecialchars($property['bedrooms']); ?>"
            required
        >

        <label for="bathrooms">Bathrooms</label>
        <input
            type="number"
            id="bathrooms"
            name="bathrooms"
            min="0"
            value="<?php echo htmlspecialchars($property['bathrooms']); ?>"
            required
        >

        <label for="area">Area (sq.ft.)</label>
        <input
            type="number"
            id="area"
            name="area"
            min="0"
            step="any"
            value="<?php echo htmlspecialchars($property['area']); ?>"
            required
        >

        <label for="listing_type">Listing Type</label>
        <select id="listing_type" name="listing_type" required>
            <option
                value="Sale"
                <?php echo $property['listing_type'] == 'Sale' ? 'selected' : ''; ?>
            >Sale</option>

            <option
                value="Rent"
                <?php echo $property['listing_type'] == 'Rent' ? 'selected' : ''; ?>
            >Rent</option>
        </select>

        <label>Current Property Image</label>
        <div class="current-image">
            <?php echo htmlspecialchars($property['image']); ?>
        </div>

        <label for="image">Choose New Property Image</label>
        <input
            type="file"
            id="image"
            name="image"
            accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
        >

        <p class="hint">
            Optional. Choose a JPG, PNG or WEBP image up to 5 MB.
            Leave this field empty to keep the current image.
        </p>

        <label for="description">Description</label>
        <textarea
            id="description"
            name="description"
            rows="5"
            required
        ><?php echo htmlspecialchars($property['description']); ?></textarea>

        <button type="submit" name="update_property">
            Update Property
        </button>

    </form>

    <?php if (isset($_SESSION['admin'])) { ?>
        <a href="manage_properties.php" class="back">
            ← Back to Manage Properties
        </a>
    <?php } else { ?>
        <a href="my_properties.php" class="back">
            ← Back to My Properties
        </a>
    <?php } ?>

</div>

</body>
</html>
