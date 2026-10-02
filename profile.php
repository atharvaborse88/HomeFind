<?php

session_start();

include "includes/db.php";


if (!isset($_SESSION['user_id'])) {

    header("Location: user_login.php");
    exit;

}


$user_id = $_SESSION['user_id'];


$sql = "SELECT * FROM users WHERE id = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $user_id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$user = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

?>


<!DOCTYPE html>
<html>

<head>

    <title>My Profile - HomeFind</title>

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

        .profile-container {
            max-width: 600px;
            margin: 60px auto;
            padding: 0 20px;
        }

        .profile-card {
            background: white;
            padding: 35px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }

        .profile-card h1 {
            text-align: center;
            margin-bottom: 30px;
        }

        .profile-info {
            margin-bottom: 20px;
            padding: 15px;
            background: #f5f7fa;
            border-radius: 8px;
        }

        .profile-info strong {
            display: block;
            margin-bottom: 6px;
            color: #666;
            font-size: 14px;
        }

        .profile-info span {
            font-size: 17px;
        }

        .buttons {
            display: flex;
            gap: 10px;
            margin-top: 25px;
        }

        .buttons a {
            flex: 1;
            text-align: center;
            text-decoration: none;
            padding: 12px;
            border-radius: 6px;
            font-weight: bold;
        }

        .favorites-btn {
            background: #2563eb;
            color: white;
        }

        .logout-btn {
            background: #fee2e2;
            color: #dc2626;
        }
        .edit-btn {
            background: #eaf2ff;
            color: #2563eb;
        }
        .password-btn {
            background: #fef3c7;
            color: #92400e;
        }

        @media (max-width: 600px) {

            .navbar {
                padding: 15px 5%;
            }

            .logo {
                font-size: 24px;
            }

            .profile-container {
                margin-top: 30px;
            }

            .profile-card {
                padding: 25px;
            }

            .buttons {
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

    <a href="index.php" class="back-link">
        ← Back to Home
    </a>

</nav>


<div class="profile-container">

    <div class="profile-card">

        <h1>👤 My Profile</h1>


        <div class="profile-info">

            <strong>Name</strong>

            <span>
                <?php echo htmlspecialchars($user['name']); ?>
            </span>

        </div>


        <div class="profile-info">

            <strong>Email</strong>

            <span>
                <?php echo htmlspecialchars($user['email']); ?>
            </span>

        </div>

<div class="buttons">

    <a href="edit_profile.php" class="edit-btn">
        ✏️ Edit Profile
    </a>

    <a href="change_password.php" class="password-btn">
        🔐 Change Password
    </a>

    <a href="favorites.php" class="favorites-btn">
        ❤️ My Favorites
    </a>

    <a href="logout.php" class="logout-btn">
        🚪 Logout
    </a>

</div>
    </div>

</div>


</body>

</html>