<?php

session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include "includes/db.php";


/* =========================
   DASHBOARD STATISTICS
   ========================= */

$property_count = 0;
$user_count = 0;
$enquiry_count = 0;


/* Total Properties */

$result_properties = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM properties"
);

if ($result_properties) {

    $row = mysqli_fetch_assoc($result_properties);

    $property_count = $row['total'];
}


/* Total Users */

$result_users = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM users"
);

if ($result_users) {

    $row = mysqli_fetch_assoc($result_users);

    $user_count = $row['total'];
}


/* Total Enquiries */

$result_enquiries_count = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM enquiries"
);

if ($result_enquiries_count) {

    $row = mysqli_fetch_assoc($result_enquiries_count);

    $enquiry_count = $row['total'];
}


/* =========================
   GET CUSTOMER ENQUIRIES
   ========================= */

$sql = "SELECT enquiries.*, properties.title
        FROM enquiries
        LEFT JOIN properties
        ON enquiries.property_id = properties.id
        ORDER BY enquiries.id DESC";

$result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>

<html>

<head>

    <title>Admin Dashboard - HomeFind</title>


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


        /* =========================
           HEADER
           ========================= */

        .header {

            background: white;

            padding: 20px 8%;

            display: flex;

            justify-content: space-between;

            align-items: center;

            box-shadow: 0 2px 8px rgba(0,0,0,0.08);

        }


        .logo {

            font-size: 28px;

            font-weight: bold;

            color: #2563eb;

        }


        .logout {

            color: #dc2626;

            text-decoration: none;

            font-weight: bold;

        }


        .logout:hover {

            text-decoration: underline;

        }


        /* =========================
           MAIN CONTAINER
           ========================= */

        .container {

            width: 90%;

            max-width: 1200px;

            margin: 40px auto;

        }


        /* =========================
           STATISTICS
           ========================= */

        .stats {

            display: grid;

            grid-template-columns: repeat(3, 1fr);

            gap: 20px;

            margin-bottom: 30px;

        }


        .stat-card {

            background: white;

            padding: 25px;

            border-radius: 12px;

            box-shadow: 0 3px 12px rgba(0,0,0,0.08);

            display: flex;

            align-items: center;

            gap: 20px;

        }


        .stat-icon {

            width: 60px;

            height: 60px;

            border-radius: 12px;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 28px;

            background: #eff6ff;

        }


        .stat-info h3 {

            margin: 0 0 5px 0;

            font-size: 15px;

            color: #666;

            font-weight: normal;

        }


        .stat-info p {

            margin: 0;

            font-size: 28px;

            font-weight: bold;

            color: #2563eb;

        }


        /* =========================
           BUTTONS
           ========================= */

        .top-buttons {

            margin-bottom: 25px;

        }


        .add-btn,
        .manage-btn {

            display: inline-block;

            color: white;

            padding: 12px 18px;

            text-decoration: none;

            border-radius: 6px;

            font-weight: bold;

        }


        .add-btn {

            background: #2563eb;

        }


        .add-btn:hover {

            background: #1d4ed8;

        }


        .manage-btn {

            background: #16a34a;

            margin-left: 10px;

        }


        .manage-btn:hover {

            background: #15803d;

        }


        /* =========================
           HEADING
           ========================= */

        h1 {

            margin-bottom: 20px;

        }


        /* =========================
           TABLE
           ========================= */

        .table-wrapper {

            overflow-x: auto;

            background: white;

            border-radius: 12px;

            box-shadow: 0 3px 12px rgba(0,0,0,0.08);

        }


        table {

            width: 100%;

            border-collapse: collapse;

            min-width: 900px;

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


        /* =========================
           DELETE BUTTON
           ========================= */

        .delete-btn {

            display: inline-block;

            background: #dc2626;

            color: white;

            padding: 8px 12px;

            text-decoration: none;

            border-radius: 5px;

            font-weight: bold;

            font-size: 13px;

        }


        .delete-btn:hover {

            background: #b91c1c;

        }


        /* =========================
           NO DATA
           ========================= */

        .no-data {

            background: white;

            padding: 40px;

            text-align: center;

            border-radius: 12px;

            box-shadow: 0 3px 12px rgba(0,0,0,0.08);

            color: #666;

        }


        /* =========================
           MOBILE
           ========================= */

        @media (max-width: 800px) {

            .stats {

                grid-template-columns: 1fr;

            }


            .header {

                padding: 18px 5%;

            }


            .logo {

                font-size: 23px;

            }


            .container {

                width: 94%;

                margin-top: 25px;

            }


            .manage-btn {

                margin-left: 0;

                margin-top: 10px;

            }

        }

    </style>


</head>


<body>


<!-- =========================
     HEADER
     ========================= -->

<div class="header">


    <div class="logo">

        🏠 HomeFind Admin

    </div>


    <a href="logout.php" class="logout">

        Logout

    </a>


</div>



<!-- =========================
     MAIN
     ========================= -->

<div class="container">


    <!-- =========================
         STATISTICS
         ========================= -->

    <div class="stats">


        <div class="stat-card">


            <div class="stat-icon">

                🏠

            </div>


            <div class="stat-info">

                <h3>Total Properties</h3>

                <p>
                    <?php echo $property_count; ?>
                </p>

            </div>


        </div>



        <div class="stat-card">


            <div class="stat-icon">

                👥

            </div>


            <div class="stat-info">

                <h3>Total Users</h3>

                <p>
                    <?php echo $user_count; ?>
                </p>

            </div>


        </div>



        <div class="stat-card">


            <div class="stat-icon">

                📩

            </div>


            <div class="stat-info">

                <h3>Total Enquiries</h3>

                <p>
                    <?php echo $enquiry_count; ?>
                </p>

            </div>


        </div>


    </div>



    <!-- =========================
         ADMIN BUTTONS
         ========================= -->

    <div class="top-buttons">


        <a
            href="add_property.php"
            class="add-btn"
        >

            + Add New Property

        </a>



        <a
            href="manage_properties.php"
            class="manage-btn"
        >

            🏠 Manage Properties

        </a>


    </div>



    <!-- =========================
         ENQUIRIES
         ========================= -->

    <h1>

        📩 Customer Enquiries

    </h1>



    <?php if ($result && mysqli_num_rows($result) > 0) { ?>


        <div class="table-wrapper">


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


                    <th>
                        Action
                    </th>


                </tr>



                <?php while ($enquiry = mysqli_fetch_assoc($result)) { ?>


                    <tr>


                        <td>

                            <?php
                            echo htmlspecialchars(
                                $enquiry['id']
                            );
                            ?>

                        </td>



                        <td>

                            <?php
                            echo htmlspecialchars(
                                $enquiry['title'] ?? 'Property Deleted'
                            );
                            ?>

                        </td>



                        <td>

                            <?php
                            echo htmlspecialchars(
                                $enquiry['name']
                            );
                            ?>

                        </td>



                        <td>

                            <?php
                            echo htmlspecialchars(
                                $enquiry['email']
                            );
                            ?>

                        </td>



                        <td>

                            <?php
                            echo htmlspecialchars(
                                $enquiry['phone']
                            );
                            ?>

                        </td>



                        <td>

                            <?php
                            echo htmlspecialchars(
                                $enquiry['message']
                            );
                            ?>

                        </td>



                        <td>

                            <?php
                            echo htmlspecialchars(
                                $enquiry['created_at']
                            );
                            ?>

                        </td>



                        <td>

                            <a
                                href="delete_enquiry.php?id=<?php echo (int)$enquiry['id']; ?>"
                                class="delete-btn"
                                onclick="return confirm('Are you sure you want to delete this enquiry?');"
                            >

                                🗑️ Delete

                            </a>

                        </td>


                    </tr>


                <?php } ?>


            </table>


        </div>


    <?php } else { ?>


        <div class="no-data">

            📩 No enquiries found.

        </div>


    <?php } ?>


</div>


</body>

</html>