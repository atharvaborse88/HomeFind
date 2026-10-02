<?php

session_start();

if (isset($_POST['login'])) {

    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($username == "admin" && $password == "admin123") {

        $_SESSION['admin'] = true;

        header("Location: admin.php");
        exit();

    } else {

        $error = "Invalid username or password.";

    }
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Admin Login - HomeFind</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background: #f5f7fb;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .login-box {
            background: white;
            width: 350px;
            padding: 35px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .logo {
            text-align: center;
            font-size: 28px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 25px;
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
        }

        input {
            width: 100%;
            box-sizing: border-box;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
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

        .error {
            color: red;
            text-align: center;
            margin-bottom: 15px;
        }

    </style>

</head>

<body>

    <div class="login-box">

        <div class="logo">
            🏠 HomeFind
        </div>

        <h2>Admin Login</h2>

        <?php if (isset($error)) { ?>

            <div class="error">
                <?php echo $error; ?>
            </div>

        <?php } ?>

        <form method="POST">

            <input
                type="text"
                name="username"
                placeholder="Username"
                required
            >

            <input
                type="password"
                name="password"
                placeholder="Password"
                required
            >

            <button type="submit" name="login">
                Login
            </button>

        </form>

    </div>

</body>

</html>