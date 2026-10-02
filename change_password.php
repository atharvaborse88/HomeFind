<?php

session_start();

include "includes/db.php";


if (!isset($_SESSION['user_id'])) {

    header("Location: user_login.php");
    exit;

}


$user_id = $_SESSION['user_id'];

$message = "";
$error = "";


if (isset($_POST['change_password'])) {

    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];


    if ($current_password == "" || $new_password == "" || $confirm_password == "") {

        $error = "All fields are required.";

    } elseif ($new_password !== $confirm_password) {

        $error = "New passwords do not match.";

    } elseif (strlen($new_password) < 6) {

        $error = "New password must be at least 6 characters.";

    } else {


        $sql = "SELECT password FROM users WHERE id = ?";

        $stmt = mysqli_prepare($conn, $sql);

        mysqli_stmt_bind_param($stmt, "i", $user_id);

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        $user = mysqli_fetch_assoc($result);

        mysqli_stmt_close($stmt);


        if (!$user || !password_verify($current_password, $user['password'])) {

            $error = "Current password is incorrect.";

        } else {


            $hashed_password = password_hash(
                $new_password,
                PASSWORD_DEFAULT
            );


            $sql = "UPDATE users
                    SET password = ?
                    WHERE id = ?";

            $stmt = mysqli_prepare($conn, $sql);

            mysqli_stmt_bind_param(
                $stmt,
                "si",
                $hashed_password,
                $user_id
            );


            if (mysqli_stmt_execute($stmt)) {

                $message = "Password changed successfully.";

            } else {

                $error = "Unable to change password.";

            }

            mysqli_stmt_close($stmt);

        }

    }

}

?>


<!DOCTYPE html>
<html>

<head>

    <title>Change Password - HomeFind</title>

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
            max-width: 500px;
            margin: 60px auto;
            padding: 0 20px;
        }

        .card {
            background: white;
            padding: 35px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 12px;
            margin-bottom: 18px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 15px;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background: #1d4ed8;
        }

        .message {
            background: #dcfce7;
            color: #166534;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            text-align: center;
        }

        .error {
            background: #fee2e2;
            color: #b91c1c;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            text-align: center;
        }

        .cancel {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #666;
            text-decoration: none;
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

            .card {
                padding: 25px;
            }

        }

    </style>

</head>

<body>


<nav class="navbar">

    <div class="logo">
        🏠 HomeFind
    </div>

    <a href="profile.php" class="back-link">
        ← Back to Profile
    </a>

</nav>


<div class="container">

    <div class="card">

        <h1>🔐 Change Password</h1>


        <?php if ($message != "") { ?>

            <div class="message">
                <?php echo htmlspecialchars($message); ?>
            </div>

        <?php } ?>


        <?php if ($error != "") { ?>

            <div class="error">
                <?php echo htmlspecialchars($error); ?>
            </div>

        <?php } ?>


        <form method="POST">

            <label>Current Password</label>

            <input
                type="password"
                name="current_password"
                placeholder="Enter current password"
                required
            >


            <label>New Password</label>

            <input
                type="password"
                name="new_password"
                placeholder="Enter new password"
                required
            >


            <label>Confirm New Password</label>

            <input
                type="password"
                name="confirm_password"
                placeholder="Confirm new password"
                required
            >


            <button type="submit" name="change_password">
                🔐 Change Password
            </button>

        </form>


        <a href="profile.php" class="cancel">
            Cancel
        </a>

    </div>

</div>


</body>

</html>