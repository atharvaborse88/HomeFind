<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - HomeFind</title>

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f5f7fa;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            width: 90%;
            max-width: 850px;
            background: white;
            padding: 45px;
            border-radius: 15px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.1);
        }

        .logo {
            text-align: center;
            font-size: 32px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 10px;
        }

        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 35px;
        }

        .login-options {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
        }

        .login-card {
            border: 1px solid #ddd;
            border-radius: 12px;
            padding: 35px 25px;
            text-align: center;
            transition: 0.2s;
        }

        .login-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }

        .icon {
            font-size: 50px;
            margin-bottom: 15px;
        }

        .login-card h2 {
            margin-bottom: 10px;
        }

        .login-card p {
            color: #666;
            margin-bottom: 25px;
            line-height: 1.5;
        }

        .login-btn {
            display: inline-block;
            width: 100%;
            padding: 13px;
            background: #2563eb;
            color: white;
            text-decoration: none;
            border-radius: 7px;
            font-weight: bold;
        }

        .login-btn:hover {
            background: #1d4ed8;
        }

        .admin-btn {
            background: #111827;
        }

        .admin-btn:hover {
            background: #000;
        }

        .home-link {
            display: block;
            text-align: center;
            margin-top: 30px;
            color: #2563eb;
            text-decoration: none;
            font-weight: bold;
        }

        @media (max-width: 600px) {

            .container {
                padding: 30px 20px;
            }

            .login-options {
                grid-template-columns: 1fr;
            }

            .logo {
                font-size: 28px;
            }

        }

    </style>

</head>

<body>

    <div class="container">

        <div class="logo">
            🏠 HomeFind
        </div>

        <p class="subtitle">
            Choose how you want to login
        </p>

        <div class="login-options">

            <!-- USER LOGIN -->

            <div class="login-card">

                <div class="icon">
                    👤
                </div>

                <h2>
                    User Login
                </h2>

                <p>
                    Login to your account to search properties,
                    save favorites and post your own property.
                </p>

                <a href="user_login.php" class="login-btn">
                    Login as User
                </a>

            </div>


            <!-- ADMIN LOGIN -->

            <div class="login-card">

                <div class="icon">
                    🛡️
                </div>

                <h2>
                    Admin Login
                </h2>

                <p>
                    Login to the administration panel to
                    manage, approve and reject properties.
                </p>

                <a href="login.php" class="login-btn admin-btn">
                    Login as Admin
                </a>

            </div>

        </div>


        <a href="index.php" class="home-link">
            ← Back to Home
        </a>

    </div>

</body>

</html>