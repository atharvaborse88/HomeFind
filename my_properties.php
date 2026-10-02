<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: user_login.php");
    exit();
}

include "includes/db.php";

$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM properties
        WHERE user_id = ?
        ORDER BY id DESC";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $user_id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>My Properties - HomeFind</title>

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f5f7fa;
            color: #222;
        }

        .navbar {
            background: white;
            padding: 18px 8%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }

        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #2563eb;
        }

        .back-link {
            text-decoration: none;
            color: #2563eb;
            font-weight: bold;
        }

        .container {
            width: 90%;
            max-width: 1100px;
            margin: 45px auto;
        }

        .top-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .top-section h1 {
            font-size: 30px;
        }

        .add-btn {
            background: #2563eb;
            color: white;
            text-decoration: none;
            padding: 12px 18px;
            border-radius: 6px;
            font-weight: bold;
        }

        .add-btn:hover {
            background: #1d4ed8;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
        }

        .card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 3px 12px rgba(0,0,0,0.08);
        }

        .card-image {
            height: 200px;
            overflow: hidden;
        }

        .card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .card-content {
            padding: 20px;
        }

        .card-content h2 {
            margin-bottom: 10px;
            font-size: 20px;
        }

        .location {
            color: #666;
            margin-bottom: 10px;
        }

        .type {
            display: inline-block;
            background: #eff6ff;
            color: #2563eb;
            padding: 5px 9px;
            border-radius: 5px;
            font-size: 13px;
            margin-bottom: 10px;
        }

        .price {
            color: #2563eb;
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .details {
            color: #666;
            font-size: 14px;
            line-height: 1.6;
        }

        /* STATUS */

        .status {
            display: inline-block;
            padding: 7px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 12px;
        }

        .pending {
            background: #fef3c7;
            color: #92400e;
        }

        .approved {
            background: #dcfce7;
            color: #166534;
        }

        .rejected {
            background: #fee2e2;
            color: #991b1b;
        }

        .actions {
            display: flex;
            gap: 8px;
            margin-top: 18px;
        }

        .view-btn,
        .edit-btn,
        .delete-btn {
            flex: 1;
            text-align: center;
            padding: 10px 6px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            font-size: 14px;
        }

        .view-btn {
            background: #eff6ff;
            color: #2563eb;
        }

        .edit-btn {
            background: #fef3c7;
            color: #b45309;
        }

        .delete-btn {
            background: #fee2e2;
            color: #dc2626;
        }

        .view-btn:hover {
            background: #dbeafe;
        }

        .edit-btn:hover {
            background: #fde68a;
        }

        .delete-btn:hover {
            background: #fecaca;
        }

        .empty {
            background: white;
            padding: 50px 20px;
            text-align: center;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.08);
        }

        .empty h2 {
            margin-bottom: 10px;
        }

        .empty p {
            color: #666;
            margin-bottom: 20px;
        }

        @media (max-width: 900px) {

            .cards {
                grid-template-columns: repeat(2, 1fr);
            }

        }

        @media (max-width: 600px) {

            .navbar {
                padding: 15px 5%;
            }

            .logo {
                font-size: 24px;
            }

            .container {
                margin-top: 30px;
            }

            .top-section {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .top-section h1 {
                font-size: 26px;
            }

            .cards {
                grid-template-columns: 1fr;
            }

            .actions {
                flex-direction: column;
            }

        }

    </style>

</head>

<body>


<nav class="navbar">

    <div class="logo">
        🏠 HomeFind
    </div>

    <a href="dashboard.php" class="back-link">
        ← Dashboard
    </a>

</nav>


<div class="container">


    <div class="top-section">

        <h1>
            🏠 My Properties
        </h1>

        <a href="add_property.php" class="add-btn">
            + Post Property
        </a>

    </div>


    <?php if (mysqli_num_rows($result) == 0) { ?>


        <div class="empty">

            <h2>
                No Properties Yet
            </h2>

            <p>
                You haven't posted any properties yet.
            </p>

            <a href="add_property.php" class="add-btn">
                + Post Your First Property
            </a>

        </div>


    <?php } else { ?>


        <div class="cards">


            <?php while ($property = mysqli_fetch_assoc($result)) { ?>


                <?php

                $status = $property['status'] ?? 'Pending';

                ?>


                <div class="card">


                    <div class="card-image">

                        <img
                            src="images/<?php
                                echo htmlspecialchars(
                                    trim($property['image'])
                                );
                            ?>"
                            alt="Property Image"
                        >

                    </div>


                    <div class="card-content">


                        <!-- STATUS -->

                        <?php if ($status == 'Approved') { ?>

                            <span class="status approved">
                                🟢 Approved
                            </span>

                        <?php } elseif ($status == 'Rejected') { ?>

                            <span class="status rejected">
                                🔴 Rejected
                            </span>

                        <?php } else { ?>

                            <span class="status pending">
                                🟡 Pending Approval
                            </span>

                        <?php } ?>


                        <span class="type">

                            <?php
                            echo htmlspecialchars(
                                $property['listing_type']
                            );
                            ?>

                        </span>
                        <p style="font-size:13px; color:#888;">
                            Property ID: <?php echo (int)$property['id']; ?>
                        </p>

                        <h2>

                            <?php
                            echo htmlspecialchars(
                                $property['title']
                            );
                            ?>

                        </h2>


                        <p class="location">

                            📍
                            <?php
                            echo htmlspecialchars(
                                $property['location']
                            );
                            ?>

                        </p>


                        <p class="price">

                            ₹<?php
                            echo number_format(
                                $property['price']
                            );
                            ?>

                        </p>


                        <p class="details">

                            🏠
                            <?php
                            echo htmlspecialchars(
                                $property['property_type'] ?? 'Property'
                            );
                            ?>

                            <br>

                            🛏️
                            <?php
                            echo htmlspecialchars(
                                $property['bedrooms']
                            );
                            ?>
                            Bedrooms

                            &nbsp; | &nbsp;

                            🚿
                            <?php
                            echo htmlspecialchars(
                                $property['bathrooms']
                            );
                            ?>
                            Bathrooms

                            <br>

                            📐
                            <?php
                            echo htmlspecialchars(
                                $property['area']
                            );
                            ?>
                            sq.ft.

                        </p>


                        <div class="actions">


                            <a
                                href="property.php?id=<?php
                                    echo (int)$property['id'];
                                ?>"
                                class="view-btn"
                            >
                                👁️ View
                            </a>


                            <a
                                href="edit_property.php?id=<?php
                                    echo (int)$property['id'];
                                ?>"
                                class="edit-btn"
                            >
                                ✏️ Edit
                            </a>


                            <a
                                href="delete_property.php?id=<?php
                                    echo (int)$property['id'];
                                ?>"
                                class="delete-btn"
                                onclick="return confirm('Are you sure you want to delete this property?');"
                            >
                                🗑️ Delete
                            </a>


                        </div>


                    </div>


                </div>


            <?php } ?>


        </div>


    <?php } ?>


</div>


</body>

</html>