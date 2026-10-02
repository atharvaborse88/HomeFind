<?php

session_start();


/* =========================
   ADMIN LOGIN CHECK
   ========================= */

if (!isset($_SESSION['admin'])) {

    header("Location: login.php");
    exit();

}


include "includes/db.php";


/* =========================
   GET ALL PROPERTIES
   ========================= */

$sql = "SELECT *
        FROM properties
        ORDER BY id DESC";

$result = mysqli_query($conn, $sql);


if (!$result) {

    die("Database error: " . mysqli_error($conn));

}

?>


<!DOCTYPE html>
<html>

<head>

    <title>Manage Properties - HomeFind Admin</title>


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
           CONTAINER
           ========================= */

        .container {

            width: 94%;

            max-width: 1400px;

            margin: 40px auto;

        }


        .top-section {

            display: flex;

            justify-content: space-between;

            align-items: center;

            margin-bottom: 25px;

        }


        .top-section h1 {

            margin: 0;

            font-size: 30px;

        }


        .back-btn {

            background: #2563eb;

            color: white;

            padding: 11px 17px;

            text-decoration: none;

            border-radius: 6px;

            font-weight: bold;

        }


        .back-btn:hover {

            background: #1d4ed8;

        }


        /* =========================
           TABLE
           ========================= */

        .table-container {

            background: white;

            border-radius: 12px;

            overflow-x: auto;

            box-shadow: 0 4px 15px rgba(0,0,0,0.08);

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

            border-bottom: 1px solid #e5e7eb;

            vertical-align: middle;

        }


        tr:hover {

            background: #f8fafc;

        }


        /* =========================
           IMAGE
           ========================= */

        .property-image {

            width: 90px;

            height: 65px;

            object-fit: cover;

            border-radius: 6px;

        }


        /* =========================
           TITLE
           ========================= */

        .title {

            font-weight: bold;

        }


        /* =========================
           PRICE
           ========================= */

        .price {

            color: #2563eb;

            font-weight: bold;

        }


        /* =========================
           TYPE
           ========================= */

        .type {

            display: inline-block;

            background: #eff6ff;

            color: #2563eb;

            padding: 5px 9px;

            border-radius: 5px;

            font-size: 13px;

        }


        /* =========================
           STATUS
           ========================= */

        .status {

            display: inline-block;

            padding: 6px 10px;

            border-radius: 6px;

            font-weight: bold;

            font-size: 13px;

        }


        .pending {

            background: #fef3c7;

            color: #b45309;

        }


        .approved {

            background: #dcfce7;

            color: #15803d;

        }


        .rejected {

            background: #fee2e2;

            color: #dc2626;

        }


        /* =========================
           ACTIONS
           ========================= */

        .actions {

            display: flex;

            gap: 6px;

            flex-wrap: wrap;

        }


        .action-btn {

            display: inline-block;

            padding: 7px 10px;

            border-radius: 5px;

            text-decoration: none;

            color: white;

            font-size: 13px;

            font-weight: bold;

        }


        /* VIEW */

        .view-btn {

            background: #2563eb;

        }


        .view-btn:hover {

            background: #1d4ed8;

        }


        /* EDIT */

        .edit-btn {

            background: #d97706;

        }


        .edit-btn:hover {

            background: #b45309;

        }


        /* DELETE */

        .delete-btn {

            background: #7f1d1d;

        }


        .delete-btn:hover {

            background: #991b1b;

        }


        /* APPROVE */

        .approve-btn {

            background: #16a34a;

        }


        .approve-btn:hover {

            background: #15803d;

        }


        /* REJECT */

        .reject-btn {

            background: #dc2626;

        }


        .reject-btn:hover {

            background: #b91c1c;

        }


        /* =========================
           NO DATA
           ========================= */

        .no-data {

            background: white;

            padding: 50px;

            text-align: center;

            border-radius: 12px;

            box-shadow: 0 4px 15px rgba(0,0,0,0.08);

        }


        .no-data h2 {

            margin-bottom: 10px;

        }


        .no-data p {

            color: #666;

        }


        /* =========================
           MOBILE
           ========================= */

        @media (max-width: 700px) {

            .header {

                padding: 15px 5%;

            }


            .logo {

                font-size: 23px;

            }


            .container {

                width: 95%;

                margin-top: 25px;

            }


            .top-section {

                flex-direction: column;

                align-items: flex-start;

                gap: 15px;

            }


            .top-section h1 {

                font-size: 25px;

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


    <a
        href="logout.php"
        class="logout"
    >

        Logout

    </a>


</div>


<!-- =========================
     MAIN CONTAINER
     ========================= -->

<div class="container">


    <div class="top-section">


        <h1>

            🏠 Manage Properties

        </h1>


        <a
            href="admin.php"
            class="back-btn"
        >

            ← Admin Dashboard

        </a>


    </div>


    <?php if (mysqli_num_rows($result) == 0) { ?>


        <div class="no-data">


            <h2>

                No Properties Found

            </h2>


            <p>

                There are currently no properties in the system.

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
                        Image
                    </th>


                    <th>
                        Title
                    </th>


                    <th>
                        Location
                    </th>


                    <th>
                        Price
                    </th>


                    <th>
                        Type
                    </th>


                    <th>
                        Status
                    </th>


                    <th>
                        Actions
                    </th>


                </tr>


                <?php while ($property = mysqli_fetch_assoc($result)) { ?>


                    <tr>


                        <!-- ID -->

                        <td>

                            <?php

                            echo (int) $property['id'];

                            ?>

                        </td>


                        <!-- IMAGE -->

                        <td>

                            <img
                                src="images/<?php echo htmlspecialchars(trim($property['image'])); ?>"
                                alt="Property Image"
                                class="property-image"
                            >

                        </td>


                        <!-- TITLE -->

                        <td>

                            <span class="title">

                                <?php

                                echo htmlspecialchars(
                                    $property['title']
                                );

                                ?>

                            </span>

                        </td>


                        <!-- LOCATION -->

                        <td>

                            📍

                            <?php

                            echo htmlspecialchars(
                                $property['location']
                            );

                            ?>

                        </td>


                        <!-- PRICE -->

                        <td>

                            <span class="price">

                                ₹<?php

                                echo number_format(
                                    $property['price']
                                );

                                ?>

                            </span>

                        </td>


                        <!-- TYPE -->

                        <td>

                            <span class="type">

                                <?php

                                echo htmlspecialchars(
                                    $property['listing_type']
                                );

                                ?>

                            </span>

                        </td>


                        <!-- STATUS -->

                        <td>


                            <?php

                            if (
                                $property['status'] == 'Pending'
                            ) {

                            ?>

                                <span class="status pending">

                                    🟡 Pending

                                </span>


                            <?php

                            } elseif (
                                $property['status'] == 'Approved'
                            ) {

                            ?>

                                <span class="status approved">

                                    🟢 Approved

                                </span>


                            <?php

                            } elseif (
                                $property['status'] == 'Rejected'
                            ) {

                            ?>

                                <span class="status rejected">

                                    🔴 Rejected

                                </span>


                            <?php

                            } else {

                            ?>

                                <span class="status">

                                    <?php

                                    echo htmlspecialchars(
                                        $property['status']
                                    );

                                    ?>

                                </span>

                            <?php

                            }

                            ?>


                        </td>


                        <!-- ACTIONS -->

                        <td>


                            <div class="actions">


                                <!-- APPROVE / REJECT -->


                                <?php

                                if (
                                    $property['status'] == 'Pending'
                                ) {

                                ?>


                                    <a
                                        href="update_property_status.php?id=<?php echo (int) $property['id']; ?>&status=Approved"
                                        class="action-btn approve-btn"
                                        onclick="return confirm('Approve this property?');"
                                    >

                                        ✅ Approve

                                    </a>


                                    <a
                                        href="update_property_status.php?id=<?php echo (int) $property['id']; ?>&status=Rejected"
                                        class="action-btn reject-btn"
                                        onclick="return confirm('Reject this property?');"
                                    >

                                        ❌ Reject

                                    </a>


                                <?php

                                } elseif (
                                    $property['status'] == 'Approved'
                                ) {

                                ?>


                                    <a
                                        href="update_property_status.php?id=<?php echo (int) $property['id']; ?>&status=Rejected"
                                        class="action-btn reject-btn"
                                        onclick="return confirm('Reject this property?');"
                                    >

                                        ❌ Reject

                                    </a>


                                <?php

                                } elseif (
                                    $property['status'] == 'Rejected'
                                ) {

                                ?>


                                    <a
                                        href="update_property_status.php?id=<?php echo (int) $property['id']; ?>&status=Approved"
                                        class="action-btn approve-btn"
                                        onclick="return confirm('Approve this property?');"
                                    >

                                        ✅ Approve

                                    </a>


                                <?php

                                }

                                ?>


                                <!-- VIEW -->

                                <a
                                    href="property.php?id=<?php echo (int) $property['id']; ?>"
                                    class="action-btn view-btn"
                                >

                                    👁️ View

                                </a>


                                <!-- EDIT -->

                                <a
                                    href="edit_property.php?id=<?php echo (int) $property['id']; ?>"
                                    class="action-btn edit-btn"
                                >

                                    ✏️ Edit

                                </a>


                                <!-- DELETE -->

                                <a
                                    href="delete_property.php?id=<?php echo (int) $property['id']; ?>"
                                    class="action-btn delete-btn"
                                    onclick="return confirm('Are you sure you want to delete this property?');"
                                >

                                    🗑️ Delete

                                </a>


                            </div>


                        </td>


                    </tr>


                <?php } ?>


            </table>


        </div>


    <?php } ?>


</div>


</body>

</html>