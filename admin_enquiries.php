<?php

session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include "includes/db.php";


/* GET ALL ENQUIRIES */

$sql = "SELECT 
            enquiries.id,
            enquiries.name,
            enquiries.email,
            enquiries.phone,
            enquiries.message,
            enquiries.created_at,
            properties.title AS property_title,
            properties.location AS property_location
        FROM enquiries
        LEFT JOIN properties
        ON enquiries.property_id = properties.id
        ORDER BY enquiries.created_at DESC";


$result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Enquiries - HomeFind Admin</title>

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
            width: 94%;
            max-width: 1400px;
            margin: 40px auto;
        }

        .back-btn {
            display: inline-block;
            margin-bottom: 20px;
            text-decoration: none;
            color: #2563eb;
            font-weight: bold;
        }

        h1 {
            margin-bottom: 25px;
        }

        .table-container {
            overflow-x: auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.08);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1100px;
        }

        th {
            background: #2563eb;
            color: white;
            padding: 14px;
            text-align: left;
        }

        td {
            padding: 14px;
            border-bottom: 1px solid #ddd;
            vertical-align: top;
        }

        tr:hover {
            background: #f8fafc;
        }

        .property {
            font-weight: bold;
            color: #2563eb;
        }

        .location {
            color: #666;
            font-size: 13px;
            margin-top: 5px;
        }

        .message {
            max-width: 300px;
            line-height: 1.5;
            color: #555;
        }

        .date {
            white-space: nowrap;
            color: #666;
            font-size: 14px;
        }

        .email {
            color: #2563eb;
        }

        .phone {
            white-space: nowrap;
        }

        .empty {
            background: white;
            padding: 50px 20px;
            text-align: center;
            border-radius: 10px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.08);
        }

        .empty h2 {
            margin-bottom: 10px;
        }

        .empty p {
            color: #666;
        }

    </style>

</head>

<body>


<div class="header">

    <div class="logo">
        🏠 HomeFind Admin
    </div>

</div>


<div class="container">


    <a href="admin.php" class="back-btn">
        ← Back to Admin
    </a>


    <h1>
        📩 Property Enquiries
    </h1>


    <?php if (mysqli_num_rows($result) == 0) { ?>

        <div class="empty">

            <h2>
                No Enquiries Found
            </h2>

            <p>
                There are currently no enquiries.
            </p>

        </div>

    <?php } else { ?>


        <div class="table-container">

            <table>

                <tr>

                    <th>
                        ID
                    </th>

                    <th>
                        Property
                    </th>

                    <th>
                        Name
                    </th>

                    <th>
                        Email
                    </th>

                    <th>
                        Phone
                    </th>

                    <th>
                        Message
                    </th>

                    <th>
                        Date
                    </th>

                </tr>


                <?php while ($enquiry = mysqli_fetch_assoc($result)) { ?>

                    <tr>

                        <td>

                            <?php
                            echo (int)$enquiry['id'];
                            ?>

                        </td>


                        <td>

                            <div class="property">

                                <?php
                                echo htmlspecialchars(
                                    $enquiry['property_title'] ?? 'Property Deleted'
                                );
                                ?>

                            </div>

                            <?php if (!empty($enquiry['property_location'])) { ?>

                                <div class="location">

                                    📍

                                    <?php
                                    echo htmlspecialchars(
                                        $enquiry['property_location']
                                    );
                                    ?>

                                </div>

                            <?php } ?>

                        </td>


                        <td>

                            <?php
                            echo htmlspecialchars(
                                $enquiry['name']
                            );
                            ?>

                        </td>


                        <td>

                            <span class="email">

                                <?php
                                echo htmlspecialchars(
                                    $enquiry['email']
                                );
                                ?>

                            </span>

                        </td>


                        <td>

                            <span class="phone">

                                <?php
                                echo htmlspecialchars(
                                    $enquiry['phone']
                                );
                                ?>

                            </span>

                        </td>


                        <td>

                            <div class="message">

                                <?php
                                echo nl2br(
                                    htmlspecialchars(
                                        $enquiry['message']
                                    )
                                );
                                ?>

                            </div>

                        </td>


                        <td>

                            <span class="date">

                                <?php
                                echo htmlspecialchars(
                                    $enquiry['created_at']
                                );
                                ?>

                            </span>

                        </td>

                    </tr>

                <?php } ?>

            </table>

        </div>


    <?php } ?>


</div>


</body>

</html>