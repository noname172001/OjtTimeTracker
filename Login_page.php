<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

$error = '';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Log In - OMH Cebu IT Timetracker</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #aaa;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        .wrapper {
            text-align: center;
            color: #333;
        }
        .login-box {
            background: #eee;
            border-radius: 10px;
            padding: 30px 40px;
            display: inline-block;
            text-align: left;
            min-width: 300px;
        }
        .login-box h2 {
            margin-top: 0;
            text-align: center;
        }
        .login-box label {
            display: block;
            margin: 10px 0 4px;
        }
        .login-box input[type="text"],
        .login-box input[type="password"] {
            width: 100%;
            padding: 6px 8px;
            box-sizing: border-box;
        }
        .login-box input[type="submit"] {
            margin-top: 15px;
            padding: 8px 16px;
            background: #0f0;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }
        .error {
            color: red;
            margin-top: 10px;
        }
        .logo {
            margin-bottom: 25px;
        }
        .logo img {
            max-width: 250px;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="logo">
            <!-- replace src with your actual logo file -->
            <img src="omega_logo.png" alt="Omega Healthcare">
        </div>
        <div class="login-box">
            <h2>Log In</h2>
            <?php if ($error): ?>
                <div class="error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form method="post" action="">
                <label for="email">Email:</label>
                <input type="text" id="email" name="email" required>

                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>

                <input type="submit" value="Log in">
            </form>
        </div>
    </div>
</body>
</html>