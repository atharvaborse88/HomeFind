<?php

session_start();

include "includes/db.php";


if (!isset($_SESSION['user_id'])) {

    echo "login_required";
    exit;

}


$user_id = $_SESSION['user_id'];

$property_id = $_POST['property_id'] ?? 0;

$action = $_POST['action'] ?? 'add';


if ($property_id > 0) {

    if ($action == 'remove') {

        $sql = "DELETE FROM favorites
                WHERE property_id = ?
                AND user_id = ?";

        $stmt = mysqli_prepare($conn, $sql);

        mysqli_stmt_bind_param(
            $stmt,
            "ii",
            $property_id,
            $user_id
        );

    } else {

        $sql = "INSERT INTO favorites (property_id, user_id)
                VALUES (?, ?)";

        $stmt = mysqli_prepare($conn, $sql);

        mysqli_stmt_bind_param(
            $stmt,
            "ii",
            $property_id,
            $user_id
        );
    }


    if (mysqli_stmt_execute($stmt)) {

        echo "success";

    } else {

        echo "Database error: " . mysqli_error($conn);

    }


    mysqli_stmt_close($stmt);

} else {

    echo "No property ID received";

}

?>