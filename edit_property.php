<?php

session_start();

include "includes/db.php";


/* =========================
   CHECK LOGIN
   ========================= */

if (!isset($_SESSION['user_id']) && !isset($_SESSION['admin'])) {

    header("Location: login_options.php");
    exit();

}


/* =========================
   GET PROPERTY ID
   ========================= */

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {

    die("Invalid property ID.");

}


/* =========================
   GET PROPERTY
   ========================= */

$stmt = mysqli_prepare(
    $conn,
    "SELECT * FROM properties WHERE id = ?"
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$property = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);


/* =========================
   PROPERTY NOT FOUND
   ========================= */

if (!$property) {

    die("Property not found.");

}


/* =========================
   OWNERSHIP CHECK
   ========================= */

/*
   Admin can edit any property.

   Normal user can edit only
   their own property.
*/

if (!isset($_SESSION['admin'])) {

    if (
        !isset($_SESSION['user_id']) ||
        $property['user_id'] != $_SESSION['user_id']
    ) {

        die("You are not authorized to edit this property.");

    }

}

// Check ownership
if (!isset($_SESSION['admin'])) {

    $user_id = $_SESSION['user_id'];

    if ((int)$property['user_id'] !== (int)$user_id) {

        echo "<script>

            alert('You are not allowed to edit this property.');

            window.location.href = 'my_properties.php';

        </script>";

        exit();

    }

}


// Update property
if (isset($_POST['update_property'])) {

    $title = trim($_POST['title']);
    $location = trim($_POST['location']);
    $price = (float)$_POST['price'];
    $bedrooms = (int)$_POST['bedrooms'];
    $bathrooms = (int)$_POST['bathrooms'];
    $area = (float)$_POST['area'];
    $listing_type = $_POST['listing_type'];
    $image = trim($_POST['image']);
    $description = trim($_POST['description']);


    // Update property
    if (isset($_SESSION['admin'])) {

    // Admin can edit without changing approval status

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

    // User edit → property needs admin approval again

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

mysqli_stmt_execute($update_stmt);

mysqli_stmt_close($update_stmt);

    // Redirect depending on account type
    if (isset($_SESSION['admin'])) {

        $redirect = "manage_properties.php";

    } else {

        $redirect = "my_properties.php";

    }


    echo "<script>

        alert('Property updated successfully!');

        window.location.href = '$redirect';

    </script>";

    exit();

}

?>


<!DOCTYPE html>
<html>

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

    <div class="logo">

        🏠 HomeFind

    </div>

</div>


<div class="container">

    <h1>

        ✏️ Edit Property

    </h1>


    <form method="POST">


        <label>

            Property Title

        </label>

        <input
            type="text"
            name="title"
            value="<?php echo htmlspecialchars($property['title']); ?>"
            required
        >


        <label>

            Location

        </label>

        <input
            type="text"
            name="location"
            value="<?php echo htmlspecialchars($property['location']); ?>"
            required
        >


        <label>

            Price

        </label>

        <input
            type="number"
            name="price"
            value="<?php echo htmlspecialchars($property['price']); ?>"
            required
        >


        <label>

            Bedrooms

        </label>

        <input
            type="number"
            name="bedrooms"
            value="<?php echo htmlspecialchars($property['bedrooms']); ?>"
            required
        >


        <label>

            Bathrooms

        </label>

        <input
            type="number"
            name="bathrooms"
            value="<?php echo htmlspecialchars($property['bathrooms']); ?>"
            required
        >


        <label>

            Area (sq.ft.)

        </label>

        <input
            type="number"
            name="area"
            value="<?php echo htmlspecialchars($property['area']); ?>"
            required
        >


        <label>

            Listing Type

        </label>

        <select name="listing_type" required>

            <option
                value="Sale"
                <?php
                if ($property['listing_type'] == 'Sale')
                    echo 'selected';
                ?>
            >
                Sale
            </option>

            <option
                value="Rent"
                <?php
                if ($property['listing_type'] == 'Rent')
                    echo 'selected';
                ?>
            >
                Rent
            </option>

        </select>


        <label>

            Image File Name

        </label>

        <input
            type="text"
            name="image"
            value="<?php echo htmlspecialchars($property['image']); ?>"
            required
        >


        <label>

            Description

        </label>

        <textarea
            name="description"
            rows="5"
            required
        ><?php echo htmlspecialchars($property['description']); ?></textarea>


        <button
            type="submit"
            name="update_property"
        >

            Update Property

        </button>


    </form>


    <?php if (isset($_SESSION['admin'])) { ?>

        <a
            href="manage_properties.php"
            class="back"
        >
            ← Back to Manage Properties
        </a>

    <?php } else { ?>

        <a
            href="my_properties.php"
            class="back"
        >
            ← Back to My Properties
        </a>

    <?php } ?>


</div>


</body>

</html>