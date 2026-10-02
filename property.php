<?php

session_start();

include "includes/db.php";


/* GET PROPERTY ID */

$id = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);

if (!$id || $id <= 0) {

    die("Invalid property ID.");

}


/* GET ONLY APPROVED PROPERTY */

$sql = "SELECT *
        FROM properties
        WHERE id = ?
        AND status = 'Approved'";

$stmt = mysqli_prepare(
    $conn,
    $sql
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result(
    $stmt
);

$property = mysqli_fetch_assoc(
    $result
);

mysqli_stmt_close($stmt);


/* PROPERTY NOT FOUND */

if (!$property) {

    die("Property not found or not available.");

}


/* CHECK FAVORITE STATUS */

$is_favorite = false;

if (isset($_SESSION['user_id'])) {

    $user_id = $_SESSION['user_id'];

    $favorite_stmt = mysqli_prepare(
        $conn,
        "SELECT id
         FROM favorites
         WHERE user_id = ?
         AND property_id = ?"
    );

    mysqli_stmt_bind_param(
        $favorite_stmt,
        "ii",
        $user_id,
        $id
    );

    mysqli_stmt_execute(
        $favorite_stmt
    );

    $favorite_result = mysqli_stmt_get_result(
        $favorite_stmt
    );

    if (
        mysqli_num_rows(
            $favorite_result
        ) > 0
    ) {

        $is_favorite = true;

    }

    mysqli_stmt_close(
        $favorite_stmt
    );

}


/* SEND ENQUIRY */

if (isset($_POST['send_enquiry'])) {

    $name = trim(
        $_POST['name'] ?? ''
    );

    $email = trim(
        $_POST['email'] ?? ''
    );

    $phone = trim(
        $_POST['phone'] ?? ''
    );

    $message = trim(
        $_POST['message'] ?? ''
    );


    /* BASIC VALIDATION */

    if (
        $name === '' ||
        $email === '' ||
        $phone === '' ||
        $message === ''
    ) {

        $error = "Please fill in all fields.";

    }

    elseif (
        !filter_var(
            $email,
            FILTER_VALIDATE_EMAIL
        )
    ) {

        $error = "Please enter a valid email address.";

    }

    else {

        /* INSERT ENQUIRY SAFELY */

        $sql_enquiry = "INSERT INTO enquiries
                        (
                            property_id,
                            name,
                            email,
                            phone,
                            message
                        )
                        VALUES (?, ?, ?, ?, ?)";

        $enquiry_stmt = mysqli_prepare(
            $conn,
            $sql_enquiry
        );

        mysqli_stmt_bind_param(
            $enquiry_stmt,
            "issss",
            $id,
            $name,
            $email,
            $phone,
            $message
        );


        if (
            mysqli_stmt_execute(
                $enquiry_stmt
            )
        ) {

            $success =
                "Enquiry sent successfully!";

        }

        else {

            $error =
                "Failed to send enquiry.";

        }


        mysqli_stmt_close(
            $enquiry_stmt
        );

    }

}

?>


<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>

        <?php

        echo htmlspecialchars(
            $property['title']
        );

        ?>

        - HomeFind

    </title>


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


        /* HEADER */

        .header {

            background: white;

            padding: 20px 8%;

            display: flex;

            justify-content: space-between;

            align-items: center;

            box-shadow:
                0 2px 8px
                rgba(0,0,0,0.08);

        }


        .logo {

            font-size: 28px;

            font-weight: bold;

            color: #2563eb;

        }


        .header a {

            text-decoration: none;

            color: #2563eb;

            font-weight: bold;

        }


        /* MAIN CONTAINER */

        .container {

            width: 85%;

            max-width: 1000px;

            margin: 40px auto;

            background: white;

            border-radius: 12px;

            overflow: hidden;

            box-shadow:
                0 4px 15px
                rgba(0,0,0,0.08);

        }


        /* PROPERTY IMAGE */

        .property-image {

            width: 100%;

            height: 450px;

            object-fit: cover;

            display: block;

        }


        /* DETAILS */

        .details {

            padding: 30px;

        }


        .details h1 {

            margin-top: 0;

            font-size: 32px;

        }


        .location {

            color: #555;

            font-size: 18px;

        }


        .price {

            color: #2563eb;

            font-size: 26px;

            font-weight: bold;

        }


        /* PROPERTY TYPE */

        .type {

            display: inline-block;

            background: #eff6ff;

            color: #2563eb;

            padding: 6px 12px;

            border-radius: 5px;

            font-size: 14px;

            font-weight: bold;

        }


        /* FEATURES */

        .features {

            display: flex;

            gap: 30px;

            margin: 25px 0;

            padding: 20px 0;

            border-top: 1px solid #ddd;

            border-bottom: 1px solid #ddd;

            flex-wrap: wrap;

        }


        .feature {

            font-size: 16px;

        }


        /* DESCRIPTION */

        .description {

            line-height: 1.7;

            color: #555;

            white-space: pre-line;

        }


        /* FAVORITE BUTTON */

        .favorite-btn {

            display: inline-block;

            margin-top: 20px;

            padding: 12px 20px;

            background: #ef4444;

            color: white;

            border: none;

            border-radius: 6px;

            cursor: pointer;

            font-size: 16px;

            font-weight: bold;

        }


        .favorite-btn:hover {

            background: #dc2626;

        }


        /* LOGIN FAVORITE BUTTON */

        .login-favorite {

            display: inline-block;

            margin-top: 20px;

            padding: 12px 20px;

            background: #2563eb;

            color: white;

            text-decoration: none;

            border-radius: 6px;

            font-weight: bold;

        }


        .login-favorite:hover {

            background: #1d4ed8;

        }


        /* ENQUIRY BOX */

        .enquiry-box {

            margin-top: 35px;

            padding-top: 25px;

            border-top: 1px solid #ddd;

        }


        .enquiry-box h2 {

            margin-bottom: 20px;

        }


        .enquiry-box input,

        .enquiry-box textarea {

            width: 100%;

            padding: 12px;

            margin-bottom: 15px;

            border: 1px solid #ddd;

            border-radius: 6px;

            font-size: 15px;

            font-family: Arial, sans-serif;

        }


        .enquiry-box textarea {

            resize: vertical;

        }


        .enquiry-box button {

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


        .enquiry-box button:hover {

            background: #1d4ed8;

        }


        /* SUCCESS */

        .success {

            background: #dcfce7;

            color: #166534;

            padding: 12px;

            border-radius: 6px;

            margin-bottom: 15px;

        }


        /* ERROR */

        .error {

            background: #fee2e2;

            color: #991b1b;

            padding: 12px;

            border-radius: 6px;

            margin-bottom: 15px;

        }


        /* BACK BUTTON */

        .back-btn {

            display: inline-block;

            margin-top: 25px;

            padding: 12px 20px;

            background: #2563eb;

            color: white;

            text-decoration: none;

            border-radius: 6px;

        }


        .back-btn:hover {

            background: #1d4ed8;

        }


        /* MOBILE */

        @media (max-width: 600px) {


            .header {

                padding: 15px 5%;

            }


            .logo {

                font-size: 24px;

            }


            .container {

                width: 94%;

                margin: 25px auto;

            }


            .property-image {

                height: 280px;

            }


            .details {

                padding: 20px;

            }


            .details h1 {

                font-size: 26px;

            }


            .features {

                gap: 15px;

            }


        }


    </style>

</head>


<body>


<!-- HEADER -->

<div class="header">


    <div class="logo">

        🏠 HomeFind

    </div>


    <a href="index.php">

        Home

    </a>


</div>



<!-- PROPERTY -->

<div class="container">


    <img
        class="property-image"

        src="images/<?php
            echo htmlspecialchars(
                trim($property['image'])
            );
        ?>"

        alt="Property Image"
    >


    <div class="details">


        <!-- PROPERTY TYPE -->

        <span class="type">

            <?php

            echo htmlspecialchars(
                $property['property_type']
            );

            ?>

        </span>



        <!-- TITLE -->

        <h1>

            <?php

            echo htmlspecialchars(
                $property['title']
            );

            ?>

        </h1>



        <!-- LOCATION -->

        <p class="location">

            📍

            <?php

            echo htmlspecialchars(
                $property['location']
            );

            ?>

        </p>



        <!-- PRICE -->

        <p class="price">

            ₹<?php

            echo number_format(
                $property['price']
            );

            ?>

        </p>



        <!-- FEATURES -->

        <div class="features">


            <div class="feature">

                🛏️

                <strong>

                    <?php

                    echo htmlspecialchars(
                        $property['bedrooms']
                    );

                    ?>

                </strong>

                Bedrooms

            </div>



            <div class="feature">

                🛁

                <strong>

                    <?php

                    echo htmlspecialchars(
                        $property['bathrooms']
                    );

                    ?>

                </strong>

                Bathrooms

            </div>



            <div class="feature">

                📐

                <strong>

                    <?php

                    echo htmlspecialchars(
                        $property['area']
                    );

                    ?>

                </strong>

                sq.ft.

            </div>



            <div class="feature">

                🏷️

                <strong>

                    <?php

                    echo htmlspecialchars(
                        $property['listing_type']
                    );

                    ?>

                </strong>

            </div>


        </div>



        <!-- DESCRIPTION -->

        <h2>

            Property Description

        </h2>


        <p class="description">

            <?php

            echo htmlspecialchars(
                $property['description']
            );

            ?>

        </p>



        <!-- FAVORITE -->

        <?php if (isset($_SESSION['user_id'])) { ?>


            <form
                method="POST"
                action="favorite.php"
            >


                <input
                    type="hidden"
                    name="property_id"
                    value="<?php
                        echo $property['id'];
                    ?>"
                >


                <input
                    type="hidden"
                    name="action"
                    value="<?php
                        echo $is_favorite
                            ? 'remove'
                            : 'add';
                    ?>"
                >


                <button
                    type="submit"
                    class="favorite-btn"
                >

                    <?php

                    echo $is_favorite

                        ? "💔 Remove from Favorites"

                        : "❤️ Add to Favorites";

                    ?>

                </button>


            </form>


        <?php } else { ?>


            <a
                href="user_login.php"
                class="login-favorite"
            >

                ❤️ Login to Add Favorites

            </a>


        <?php } ?>



        <!-- ENQUIRY -->

        <div class="enquiry-box">


            <h2>

                Send an Enquiry

            </h2>


            <?php if (isset($success)) { ?>


                <div class="success">

                    <?php

                    echo htmlspecialchars(
                        $success
                    );

                    ?>

                </div>


            <?php } ?>


            <?php if (isset($error)) { ?>


                <div class="error">

                    <?php

                    echo htmlspecialchars(
                        $error
                    );

                    ?>

                </div>


            <?php } ?>



            <form
                method="POST"
                action="property.php?id=<?php
                    echo $property['id'];
                ?>"
            >


                <input
                    type="text"
                    name="name"
                    placeholder="Your Name"
                    required
                >


                <input
                    type="email"
                    name="email"
                    placeholder="Your Email"
                    required
                >


                <input
                    type="text"
                    name="phone"
                    placeholder="Your Phone Number"
                    required
                >


                <textarea
                    name="message"
                    placeholder="Your Message"
                    rows="5"
                    required
                ></textarea>


                <button
                    type="submit"
                    name="send_enquiry"
                >

                    📩 Send Enquiry

                </button>


            </form>


        </div>



        <!-- BACK -->

        <a
            href="index.php"
            class="back-btn"
        >

            ← Back to Properties

        </a>


    </div>

</div>


</body>

</html>