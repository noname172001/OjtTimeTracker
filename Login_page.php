<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

require_once 'db_config.php';

$error = '';

//Login_page

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    
    if (!empty($email) && !empty($password)) {
        $conn = getDBConnection();
        
        // Check if it's an admin login
        $stmt = $conn->prepare("SELECT admin_id, admin_email FROM admin WHERE admin_email = ? AND admin_password = ?");
        $stmt->execute([$email, $password]);
        $admin = $stmt->fetch();
        
        if ($admin) {
            // Admin login successful
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['admin_id'];
            $_SESSION['admin_name'] = 'Admin';
            $_SESSION['user_type'] = 'admin';
            
            closeDBConnection($conn);
            header('Location: admin_dashboard.php');
            exit;
        }
        
        // Check if it's a user login
        $stmt = $conn->prepare("SELECT user_id, user_name, user_email FROM users WHERE user_email = ? AND user_password = ?");
        $stmt->execute([$email, $password]);
        $user = $stmt->fetch();
        
        if ($user) {
            // User login successful
            $_SESSION['user_logged_in'] = true;
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = $user['user_name'];
            $_SESSION['user_email'] = $user['user_email'];
            $_SESSION['user_type'] = 'user';
            
            closeDBConnection($conn);
            header('Location: user_dashboard.php');
            exit;
        }
        
        // Login failed
        $error = 'Invalid email or password';
        closeDBConnection($conn);
    } else {
        $error = 'Please enter both email and password';
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Log In - OMH IT Timetracker</title>
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
        .container {
            display: flex;
            align-items: center;
            gap: 120px;
            max-width: 1200px;
        }
        .left-section {
            text-align: center;
        }
        .logo {
            margin-bottom: 60px;
        }
        .logo img {
            width: 380px;
        }
        .app-title {
            font-size: 38px;
            color: #333;
            font-weight: normal;
            line-height: 1.3;
        }
        .login-box {
            background: #f5f5f5;
            border: 2px solid #333;
            border-radius: 12px;
            padding: 50px 60px 60px 60px;
            width: 400px;
            box-sizing: border-box;
        }
        .login-box h2 {
            margin: 0 0 70px 0;
            text-align: center;
            font-size: 36px;
            font-weight: normal;
            color: #333;
        }
        .form-group {
            margin-bottom: 30px;
            text-align: center;
        }
        .form-row {
            display: inline-flex;
            align-items: center;
            gap: 15px;
        }
        .login-box label {
            font-size: 18px;
            color: #333;
            min-width: 90px;
            text-align: left;
        }
        .login-box input[type="text"],
        .login-box input[type="password"] {
            width: 190px;
            padding: 8px 10px;
            box-sizing: border-box;
            border: 1px solid #333;
            font-size: 14px;
            background: #ffffff;
        }
        .login-box input[type="text"]:focus,
        .login-box input[type="password"]:focus {
            outline: none;
            border-color: #3b9dd8;
            border-width: 2px;
        }
        .form-actions {
            text-align: right;
            margin-top: 50px;
        }
        .login-box input[type="submit"] {
            padding: 12px 50px;
            background: #00ff00;
            border: 1px solid #000;
            cursor: pointer;
            font-size: 16px;
            border-radius: 4px;
        }
        .login-box input[type="submit"]:hover {
            background: #00dd00;
        }
        .error {
            color: red;
            margin-bottom: 15px;
            font-size: 14px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="left-section">
            <div class="logo">
                <img src="/image/logo.png" alt="Omega Healthcare">
            </div>
            <div class="app-title">
                OMH Cebu IT<br>
                Timetracker | OJT
            </div>
        </div>
        
        <div class="login-box">
            <h2>Log In</h2>
            <?php if ($error): ?>
                <div class="error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form method="post" action="">
                <div class="form-group">
                    <div class="form-row">
                        <label for="email">Email:</label>
                        <input type="text" id="email" name="email" required>
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-row">
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                </div>

                <div class="form-actions">
                    <input type="submit" value="Log in">
                </div>
            </form>
        </div>
    </div>
</body>
</html>