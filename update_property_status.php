<?php

session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include "includes/db.php";

if (!isset($_GET['id']) || !isset($_GET['status'])) {
    header("Location: manage_properties.php");
    exit();
}

$id = (int) $_GET['id'];

$status = $_GET['status'];

$allowed_status = ['Approved', 'Rejected', 'Pending'];

if (!in_array($status, $allowed_status)) {
    die("Invalid status.");
}

$sql = "UPDATE properties
        SET status = ?
        WHERE id = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "si",
    $status,
    $id
);

if (mysqli_stmt_execute($stmt)) {

    mysqli_stmt_close($stmt);

    header("Location: manage_properties.php");
    exit();

} else {

    echo "Database error: " . mysqli_error($conn);

}

?>