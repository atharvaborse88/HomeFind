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
   Admin can delete any property.

   Normal user can delete only
   their own property.
*/

if (!isset($_SESSION['admin'])) {

    if (
        !isset($_SESSION['user_id']) ||
        $property['user_id'] != $_SESSION['user_id']
    ) {

        die("You are not authorized to delete this property.");

    }

}


/* =========================
   DELETE IMAGE
   ========================= */

if (!empty($property['image'])) {

    $image_path = "images/" . basename($property['image']);

    if (file_exists($image_path)) {

        unlink($image_path);

    }

}


/* =========================
   DELETE PROPERTY
   ========================= */

$delete_stmt = mysqli_prepare(
    $conn,
    "DELETE FROM properties WHERE id = ?"
);

mysqli_stmt_bind_param(
    $delete_stmt,
    "i",
    $id
);


if (mysqli_stmt_execute($delete_stmt)) {

    mysqli_stmt_close($delete_stmt);

    /*
       Send admin back to the admin property page.
       Send normal users back to My Properties.
    */

    if (isset($_SESSION['admin'])) {

        header("Location: manage_properties.php");

    } else {

        header("Location: my_properties.php");

    }

    exit();

} else {

    echo "Database error: " . mysqli_error($conn);

}

?>