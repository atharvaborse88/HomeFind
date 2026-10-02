<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: user_login.php");
    exit();
}

include "includes/db.php";

$user_id = $_SESSION['user_id'];

if (isset($_POST['add_property'])) {

    $title = $_POST['title'];
    $location = $_POST['location'];
    $property_type = $_POST['property_type'];
    $listing_type = $_POST['listing_type'];
    $price = $_POST['price'];
    $bedrooms = $_POST['bedrooms'];
    $bathrooms = $_POST['bathrooms'];
    $area = $_POST['area'];
    $description = $_POST['description'];

    /* IMAGE UPLOAD */

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {

        $image_name = $_FILES['image']['name'];
        $image_tmp = $_FILES['image']['tmp_name'];

        $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp'];

        $extension = strtolower(
            pathinfo($image_name, PATHINFO_EXTENSION)
        );

        if (!in_array($extension, $allowed_extensions)) {

            die("Invalid image format. Please upload JPG, JPEG, PNG or WEBP.");

        }

        /* Create a unique filename */

        $new_image_name = uniqid('property_', true) . '.' . $extension;

        $upload_path = "images/" . $new_image_name;

        /* Move image to images folder */

        if (!move_uploaded_file($image_tmp, $upload_path)) {

            die("Image upload failed.");

        }

    } else {

        die("Please select a property image.");

    }


    /* SAVE PROPERTY */

    $sql = "INSERT INTO properties
        (
            user_id,
            title,
            location,
            property_type,
            listing_type,
            price,
            bedrooms,
            bathrooms,
            area,
            description,
            image,
            status
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);

    $status = "Pending";

mysqli_stmt_bind_param(
    $stmt,
    "issssiiidsss",
    $user_id,
    $title,
    $location,
    $property_type,
    $listing_type,
    $price,
    $bedrooms,
    $bathrooms,
    $area,
    $description,
    $new_image_name,
    $status
);

    if (mysqli_stmt_execute($stmt)) {

        echo "<script>
                alert('Property posted successfully!');
                window.location.href='my_properties.php';
              </script>";

        exit();

    } else {

        echo "Database error: " . mysqli_error($conn);

    }

}

?>


<!DOCTYPE html>
<html>

<head>

    <title>Post Property - HomeFind</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background: #f5f7fb;
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
            text-align: center;
        }

        label {
            display: block;
            margin-top: 15px;
            margin-bottom: 6px;
            font-weight: bold;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 15px;
        }

        textarea {
            resize: vertical;
        }

        input[type="file"] {
            background: #f8fafc;
            padding: 10px;
        }

        .image-note {
            color: #666;
            font-size: 13px;
            margin-top: 6px;
        }

        button {
            margin-top: 25px;
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
            display: block;
            margin-top: 20px;
            text-align: center;
            text-decoration: none;
            color: #2563eb;
        }

    </style>

</head>


<body>


<div class="header">

    <div class="logo">
        🏠 HomeFind
    </div>

</div>


<div class="container">

    <h1>🏠 Post Your Property</h1>


    <form method="POST" enctype="multipart/form-data">


        <label>Property Title</label>

        <input
            type="text"
            name="title"
            placeholder="Example: 2 BHK Modern Apartment"
            required
        >


        <label>Location</label>

        <input
            type="text"
            name="location"
            placeholder="Example: Kalyan"
            required
        >


        <label>Property Type</label>

        <select name="property_type" required>

            <option value="">Select Property Type</option>

            <option value="Apartment">
                Apartment
            </option>

            <option value="House">
                House
            </option>

            <option value="Villa">
                Villa
            </option>

            <option value="Plot">
                Plot
            </option>

        </select>


        <label>Listing Type</label>

        <select name="listing_type" required>

            <option value="">
                Select Listing Type
            </option>

            <option value="Sale">
                Sale
            </option>

            <option value="Rent">
                Rent
            </option>

        </select>


        <label>Price</label>

        <input
            type="number"
            name="price"
            placeholder="Example: 6500000"
            required
        >


        <label>Bedrooms</label>

        <input
            type="number"
            name="bedrooms"
            placeholder="Example: 2"
            required
        >


        <label>Bathrooms</label>

        <input
            type="number"
            name="bathrooms"
            placeholder="Example: 2"
            required
        >


        <label>Area (sq.ft.)</label>

        <input
            type="number"
            name="area"
            placeholder="Example: 950"
            required
        >


        <label>Property Image</label>

        <input
            type="file"
            name="image"
            accept=".jpg,.jpeg,.png,.webp"
            required
        >

        <p class="image-note">
            JPG, JPEG, PNG and WEBP images are supported.
        </p>


        <label>Description</label>

        <textarea
            name="description"
            rows="5"
            placeholder="Describe your property..."
            required
        ></textarea>


        <button
            type="submit"
            name="add_property"
        >
            🏠 Post Property
        </button>


    </form>


    <a href="my_properties.php" class="back">
        ← Back to My Properties
    </a>


</div>


</body>

</html>