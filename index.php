<?php

session_start();

include "includes/db.php";

/* ==============================
   SEARCH FILTERS
============================== */

$location = trim($_GET['location'] ?? '');
$listing_type = trim($_GET['listing_type'] ?? '');

/* ==============================
   GET APPROVED PROPERTIES ONLY
============================== */

$sql = "SELECT * FROM properties WHERE status = 'Approved'";

$params = [];
$types = "";

if ($location != '') {
    $sql .= " AND location LIKE ?";
    $params[] = "%" . $location . "%";
    $types .= "s";
}

if ($listing_type != '') {
    $sql .= " AND listing_type = ?";
    $params[] = $listing_type;
    $types .= "s";
}

$sql .= " ORDER BY id DESC";

$stmt = mysqli_prepare($conn, $sql);

if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);


/* ==============================
   GET USER FAVORITES
============================== */

$favorites = [];

if (isset($_SESSION['user_id'])) {

    $user_id = $_SESSION['user_id'];

    $fav_stmt = mysqli_prepare(
        $conn,
        "SELECT property_id FROM favorites WHERE user_id = ?"
    );

    mysqli_stmt_bind_param(
        $fav_stmt,
        "i",
        $user_id
    );

    mysqli_stmt_execute($fav_stmt);

    $fav_result = mysqli_stmt_get_result($fav_stmt);

    while ($fav = mysqli_fetch_assoc($fav_result)) {

        $favorites[] = $fav['property_id'];

    }

    mysqli_stmt_close($fav_stmt);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>HomeFind - Find Your Perfect Property</title>


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


        /* ==============================
           NAVBAR
        ============================== */

        .navbar {

            background: white;

            padding: 18px 8%;

            display: flex;

            justify-content: space-between;

            align-items: center;

            box-shadow: 0 2px 10px rgba(0,0,0,0.08);

        }


        .logo img {

            width: 170px;

            height: auto;

            display: block;

        }


        .nav-links {

            display: flex;

            align-items: center;

            gap: 20px;

        }


        .nav-links a {

            text-decoration: none;

            color: #333;

            font-weight: 500;

        }


        .nav-links a:hover {

            color: #2563eb;

        }


        .post-btn {

            background: #2563eb;

            color: white !important;

            padding: 10px 18px;

            border-radius: 6px;

        }


        .post-btn:hover {

            background: #1d4ed8;

        }


        /* ==============================
           HERO
        ============================== */

        .hero {

            background: #eaf2ff;

            text-align: center;

            padding: 80px 20px;

        }


        .hero h1 {

            font-size: 42px;

            margin-bottom: 15px;

        }


        .hero p {

            font-size: 18px;

            color: #666;

            margin-bottom: 35px;

        }


        /* ==============================
           SEARCH
        ============================== */

        .search-box {

            background: white;

            max-width: 850px;

            margin: auto;

            padding: 15px;

            border-radius: 10px;

            box-shadow: 0 5px 20px rgba(0,0,0,0.1);

            display: flex;

            gap: 10px;

        }


        .search-box select,
        .search-box input {

            flex: 1;

            padding: 15px;

            border: 1px solid #ddd;

            border-radius: 6px;

            font-size: 15px;

        }


        .search-btn {

            background: #2563eb;

            color: white;

            border: none;

            padding: 15px 25px;

            border-radius: 6px;

            cursor: pointer;

            font-size: 15px;

        }


        .search-btn:hover {

            background: #1d4ed8;

        }


        /* ==============================
           PROPERTIES
        ============================== */

        .properties {

            padding: 50px 8%;

        }


        .properties h2 {

            text-align: center;

            margin-bottom: 30px;

            font-size: 30px;

        }


        .cards {

            display: grid;

            grid-template-columns: repeat(3, 1fr);

            gap: 25px;

        }


        /* ==============================
           PROPERTY CARD
        ============================== */

        .card {

            background: white;

            border-radius: 10px;

            overflow: hidden;

            box-shadow: 0 3px 12px rgba(0,0,0,0.08);

            transition: transform 0.2s,
                        box-shadow 0.2s;

        }


        .card:hover {

            transform: translateY(-5px);

            box-shadow: 0 8px 20px rgba(0,0,0,0.15);

        }


        .card-image {

            position: relative;

            height: 220px;

            overflow: hidden;

        }


        .card-image img {

            width: 100%;

            height: 100%;

            object-fit: cover;

            display: block;

            transition: transform 0.3s ease;

        }


        .card:hover .card-image img {

            transform: scale(1.05);

        }


        .listing-badge {

            position: absolute;

            top: 12px;

            left: 12px;

            background: #2563eb;

            color: white;

            padding: 6px 12px;

            border-radius: 5px;

            font-size: 13px;

            font-weight: bold;

        }


        .card-content {

            padding: 20px;

        }


        .card-content h3 {

            margin-bottom: 10px;

            font-size: 20px;

        }


        .location {

            color: #666;

            margin-bottom: 12px;

        }


        .price {

            color: #2563eb;

            font-size: 20px;

            font-weight: bold;

            margin-top: 12px;

        }


        /* ==============================
           VIEW DETAILS
        ============================== */

        .view-details {

            display: block;

            margin-top: 15px;

            padding-top: 12px;

            border-top: 1px solid #eee;

            color: #2563eb;

            font-weight: bold;

            font-size: 14px;

            text-decoration: none;

        }


        .view-details:hover {

            color: #1d4ed8;

            text-decoration: underline;

        }


        /* ==============================
           FAVORITE
        ============================== */

        .favorite-btn {

            margin-top: 12px;

            padding: 8px 12px;

            border: 1px solid #ddd;

            border-radius: 6px;

            color: #555;

            font-size: 14px;

            display: inline-block;

            cursor: pointer;

            background: white;

        }


        .favorite-btn:hover {

            color: #d1395a;

            border-color: #e11d48;

        }


        .favorite-btn.favorited {

            color: #d1395a;

            border-color: #e11d48;

        }


        /* ==============================
           NO RESULTS
        ============================== */

        .no-properties {

            background: white;

            max-width: 600px;

            margin: 30px auto;

            padding: 40px;

            text-align: center;

            border-radius: 10px;

            box-shadow: 0 3px 12px rgba(0,0,0,0.08);

        }


        .no-properties h3 {

            margin-bottom: 10px;

        }


        .no-properties p {

            color: #666;

        }


        /* ==============================
           RESPONSIVE
        ============================== */

        @media (max-width: 900px) {

            .cards {

                grid-template-columns: repeat(2, 1fr);

            }

        }


        @media (max-width: 700px) {

            .navbar {

                flex-direction: column;

                gap: 15px;

                padding: 15px 5%;

            }


            .logo img {

                width: 150px;

            }


            .nav-links {

                flex-wrap: wrap;

                justify-content: center;

                gap: 10px;

            }


            .nav-links a {

                font-size: 14px;

            }


            .hero {

                padding: 60px 15px;

            }


            .hero h1 {

                font-size: 32px;

            }


            .search-box {

                flex-direction: column;

            }


            .cards {

                grid-template-columns: 1fr;

            }


            .properties {

                padding: 40px 5%;

            }

        }

    </style>

</head>


<body>


<!-- ==============================
     NAVBAR
============================== -->

<nav class="navbar">


    <div class="logo">

        <img
            src="images/homefind-logo.png"
            alt="HomeFind Logo"
        >

    </div>


    <div class="nav-links">

        <a href="index.php">
            Home
        </a>

        <a href="index.php?listing_type=Sale">
            Buy
        </a>

        <a href="index.php?listing_type=Rent">
            Rent
        </a>

        <a href="favorites.php">
            ❤️ Favorites
        </a>

        <?php if (isset($_SESSION['user_id'])) { ?>

            <a href="dashboard.php">
                👤 <?php echo htmlspecialchars($_SESSION['user_name']); ?>
            </a>

            <a href="logout.php">
                Logout
            </a>

        <?php } else { ?>

            <a href="login_options.php">
                Login
            </a>

            <a href="register.php">
                Register
            </a>

        <?php } ?>

        <a href="add_property.php" class="post-btn">
            + Post Property
        </a>

    </div>

</nav>



<!-- ==============================
     HERO
============================== -->

<section class="hero">


    <h1>

        Find a place you'll love 🏠

    </h1>


    <p>

        Search thousands of properties for sale and rent

    </p>


    <form
        class="search-box"
        method="GET"
        action="index.php"
    >


        <select name="listing_type">


            <option value="">

                Buy / Rent

            </option>


            <option
                value="Sale"
                <?php
                if ($listing_type == 'Sale') {
                    echo 'selected';
                }
                ?>
            >

                Buy

            </option>


            <option
                value="Rent"
                <?php
                if ($listing_type == 'Rent') {
                    echo 'selected';
                }
                ?>
            >

                Rent

            </option>


        </select>


        <input
            type="text"
            name="location"
            placeholder="Enter city or location"
            value="<?php echo htmlspecialchars($location); ?>"
        >


        <button
            type="submit"
            class="search-btn"
        >

            Search 🔍

        </button>


    </form>


</section>



<!-- ==============================
     PROPERTIES
============================== -->

<section class="properties">


    <h2>

        <?php

        if ($listing_type == 'Sale') {

            echo "Properties for Sale";

        } elseif ($listing_type == 'Rent') {

            echo "Properties for Rent";

        } elseif ($location != '') {

            echo "Properties in " .
                 htmlspecialchars($location);

        } else {

            echo "Featured Properties";

        }

        ?>

    </h2>



    <div class="cards">


        <?php

        if (mysqli_num_rows($result) == 0) {

        ?>


            <div class="no-properties">


                <h3>

                    🏠 No Properties Found

                </h3>


                <p>

                    No approved properties match
                    your search.

                </p>


            </div>


        <?php

        } else {


            while (
                $property =
                mysqli_fetch_assoc($result)
            ) {


                $property_id =
                    (int) $property['id'];


                $is_favorite =
                    in_array(
                        $property_id,
                        $favorites
                    );


        ?>


            <div class="card">


                <!-- PROPERTY IMAGE -->

                <div class="card-image">


                    <a
                        href="property.php?id=<?php echo $property_id; ?>"
                    >


                        <img
                            src="images/<?php
                                echo htmlspecialchars(
                                    trim(
                                        $property['image']
                                    )
                                );
                            ?>"
                            alt="<?php
                                echo htmlspecialchars(
                                    $property['title']
                                );
                            ?>"
                            onerror="this.src='images/default.jpg';"
                        >


                    </a>


                    <span class="listing-badge">

                        <?php
                        echo htmlspecialchars(
                            $property['listing_type']
                        );
                        ?>

                    </span>


                </div>



                <!-- PROPERTY DETAILS -->

                <div class="card-content">


                    <h3>

                        <?php
                        echo htmlspecialchars(
                            $property['title']
                        );
                        ?>

                    </h3>


                    <p class="location">

                        📍
                        <?php
                        echo htmlspecialchars(
                            $property['location']
                        );
                        ?>

                    </p>


                    <p>

                        🛏️
                        <?php
                        echo htmlspecialchars(
                            $property['bedrooms']
                        );
                        ?>
                        Bedrooms

                        &nbsp; | &nbsp;

                        🛁
                        <?php
                        echo htmlspecialchars(
                            $property['bathrooms']
                        );
                        ?>
                        Bathrooms

                    </p>


                    <p>

                        📐
                        <?php
                        echo htmlspecialchars(
                            $property['area']
                        );
                        ?>
                        sq.ft.

                    </p>


                    <p class="price">

                        ₹<?php
                        echo number_format(
                            $property['price']
                        );
                        ?>

                    </p>


                    <a
                        href="property.php?id=<?php echo $property_id; ?>"
                        class="view-details"
                    >

                        View Details →

                    </a>



                    <!-- FAVORITE BUTTON -->

                    <button
                        type="button"
                        class="favorite-btn <?php
                            echo $is_favorite
                                ? 'favorited'
                                : '';
                        ?>"
                        data-property-id="<?php
                            echo $property_id;
                        ?>"
                    >


                        <?php

                        if ($is_favorite) {

                            echo "♥ Favorited";

                        } else {

                            echo "♡ Favorite";

                        }

                        ?>


                    </button>


                </div>


            </div>


        <?php

            }

        }

        ?>


    </div>


</section>



<!-- ==============================
     FAVORITE JAVASCRIPT
============================== -->

<script>

document
.querySelectorAll('.favorite-btn')
.forEach(button => {


    button.addEventListener(
        'click',
        function(event) {


            event.preventDefault();

            event.stopPropagation();


            const propertyId =
                this.getAttribute(
                    'data-property-id'
                );


            const isFavorited =
                this.classList.contains(
                    'favorited'
                );


            const formData =
                new FormData();


            formData.append(
                'property_id',
                propertyId
            );


            formData.append(
                'action',
                isFavorited
                    ? 'remove'
                    : 'add'
            );


            fetch(
                'favorite.php',
                {
                    method: 'POST',
                    body: formData
                }
            )


            .then(
                response =>
                    response.text()
            )


            .then(data => {


                data =
                    data.trim();


                if (
                    data === 'success'
                ) {


                    if (
                        isFavorited
                    ) {


                        this.innerHTML =
                            '♡ Favorite';


                        this.classList
                            .remove(
                                'favorited'
                            );


                    } else {


                        this.innerHTML =
                            '♥ Favorited';


                        this.classList
                            .add(
                                'favorited'
                            );

                    }


                }


                else if (
                    data ===
                    'login_required'
                ) {


                    alert(
                        'Please login to add favorites.'
                    );


                    window.location.href =
                        'user_login.php';

                }


                else {

                    console.log(data);

                }


            })


            .catch(error => {

                console.error(
                    'Favorite error:',
                    error
                );

            });


        }
    );


});

</script>


</body>

</html>