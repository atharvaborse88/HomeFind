<?php

session_start();

include "includes/db.php";


if (!isset($_SESSION['user_id'])) {

    header("Location: user_login.php");
    exit;

}


$user_id = $_SESSION['user_id'];


$sql = "SELECT properties.*
        FROM properties
        INNER JOIN favorites
        ON properties.id = favorites.property_id
        WHERE favorites.user_id = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $user_id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

?>


<!DOCTYPE html>
<html>

<head>

    <title>My Favorites - HomeFind</title>

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

        .favorites {
            padding: 50px 8%;
        }

        .favorites h1 {
            text-align: center;
            margin-bottom: 35px;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
        }

        .card {
            background: white;
            border-radius: 10px;
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

        .location {
            color: #666;
            margin: 10px 0;
        }

        .price {
            color: #2563eb;
            font-size: 20px;
            font-weight: bold;
        }

        .view-details {
            display: inline-block;
            margin-top: 15px;
            color: #2563eb;
            font-weight: bold;
            text-decoration: none;
        }

        @media (max-width: 800px) {

            .cards {
                grid-template-columns: 1fr;
            }

            .navbar {
                padding: 15px 5%;
            }

        }

    </style>

</head>

<body>


<nav class="navbar">

    <div class="logo">
        🏠 HomeFind
    </div>

    <a href="index.php" class="back-link">
        ← Back to Home
    </a>

</nav>


<section class="favorites">

    <h1>❤️ My Favorites</h1>

    <div class="cards">

        <?php

        if (mysqli_num_rows($result) == 0) {

            echo "<p>No favorite properties yet.</p>";

        } else {

            while ($property = mysqli_fetch_assoc($result)) {

        ?>

        <div class="card">

            <div class="card-image">

                <img
                    src="images/<?php echo trim($property['image']); ?>"
                    alt="Property Image"
                >

            </div>

            <div class="card-content">

                <h3>
                    <?php echo htmlspecialchars($property['title']); ?>
                </h3>

                <p class="location">
                    📍 <?php echo htmlspecialchars($property['location']); ?>
                </p>

                <p class="price">
                    ₹<?php echo number_format($property['price']); ?>
                </p>

                <a
                    href="property.php?id=<?php echo $property['id']; ?>"
                    class="view-details"
                >
                    View Details →
                </a>

            </div>

        </div>

        <?php

            }

        }

        ?>

    </div>

</section>


</body>

</html>