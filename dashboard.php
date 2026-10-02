<?php

session_start();

include "includes/db.php";


if (!isset($_SESSION['user_id'])) {

    header("Location: user_login.php");
    exit;

}


$user_id = $_SESSION['user_id'];

$user_name = $_SESSION['user_name'] ?? 'User';


/* Count user's favorites */

$fav_sql = "SELECT COUNT(*) AS total
            FROM favorites
            WHERE user_id = ?";

$fav_stmt = mysqli_prepare($conn, $fav_sql);

mysqli_stmt_bind_param($fav_stmt, "i", $user_id);

mysqli_stmt_execute($fav_stmt);

$fav_result = mysqli_stmt_get_result($fav_stmt);

$fav_data = mysqli_fetch_assoc($fav_result);

$favorites_count = $fav_data['total'];

mysqli_stmt_close($fav_stmt);

?>


<!DOCTYPE html>
<html>

<head>

    <title>Dashboard - HomeFind</title>

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

        .home-link {
            text-decoration: none;
            color: #2563eb;
            font-weight: bold;
        }

        .dashboard {
            max-width: 1000px;
            margin: 50px auto;
            padding: 0 20px;
        }

        .welcome {
            text-align: center;
            margin-bottom: 40px;
        }

        .welcome h1 {
            font-size: 32px;
            margin-bottom: 10px;
        }

        .welcome p {
            color: #666;
            font-size: 16px;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 25px;
        }

        .dashboard-card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.08);
            text-decoration: none;
            color: #222;
            transition: 0.2s;
        }

        .dashboard-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.12);
        }

        .icon {
            font-size: 35px;
            margin-bottom: 15px;
        }

        .dashboard-card h2 {
            margin-bottom: 8px;
        }

        .dashboard-card p {
            color: #666;
        }

        .count {
            margin-top: 15px;
            color: #2563eb !important;
            font-weight: bold;
        }

        .logout-card {
            color: #dc2626;
        }

        @media (max-width: 600px) {

            .navbar {
                padding: 15px 5%;
            }

            .logo {
                font-size: 24px;
            }

            .dashboard {
                margin-top: 30px;
            }

            .welcome h1 {
                font-size: 26px;
            }

            .cards {
                grid-template-columns: 1fr;
            }

        }

    </style>

</head>

<body>


<nav class="navbar">

    <div class="logo">
        🏠 HomeFind
    </div>

    <a href="index.php" class="home-link">
        ← Home
    </a>

</nav>


<main class="dashboard">


    <div class="welcome">

        <h1>
            Welcome, <?php echo htmlspecialchars($user_name); ?> 👋
        </h1>

        <p>
            Manage your HomeFind account
        </p>

    </div>


    <div class="cards">


        <!-- Profile -->

        <a href="profile.php" class="dashboard-card">

            <div class="icon">
                👤
            </div>

            <h2>
                My Profile
            </h2>

            <p>
                View and edit your account information.
            </p>

        </a>


        <!-- Favorites -->

        <a href="favorites.php" class="dashboard-card">

            <div class="icon">
                ❤️
            </div>

            <h2>
                My Favorites
            </h2>

            <p>
                View properties you have saved.
            </p>

            <p class="count">
                <?php echo $favorites_count; ?> Favorite(s)
            </p>

        </a>


        <!-- My Properties -->

        <a href="my_properties.php" class="dashboard-card">

        <div class="icon">
        🏠
    </div>

    <h2>
        My Properties
    </h2>

    <p>
        Properties you have posted.
    </p>

</a>


        <!-- Logout -->

        <a href="logout.php" class="dashboard-card logout-card">

            <div class="icon">
                🚪
            </div>

            <h2>
                Logout
            </h2>

            <p>
                Sign out of your HomeFind account.
            </p>

        </a>


    </div>


</main>


</body>

</html>