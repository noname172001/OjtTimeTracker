<?php
// login_page.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

require_once 'db_config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!empty($email) && !empty($password)) {
        $conn = getDBConnection();

        // Join login + users tables to get all needed info
        $stmt = $conn->prepare("
            SELECT l.login_id, l.user_id, l.role, l.password,
                   u.first_name, u.last_name
            FROM login l
            JOIN users u ON l.user_id = u.user_id
            WHERE l.email = ?
        ");
        $stmt->execute([$email]);
        $account = $stmt->fetch();

        if ($account && (password_verify($password, $account['password']) || $account['password'] === $password)) {
            if ($account['role'] === 'admin') {
                // Admin login
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['user_id']         = $account['user_id'];
                $_SESSION['admin_name']      = $account['first_name'] . ' ' . $account['last_name'];
                $_SESSION['user_type']       = 'admin';

                closeDBConnection($conn);
                header('Location: admin_dashboard.php');
                exit;

            } elseif ($account['role'] === 'intern') {
                // Intern login
                $_SESSION['user_logged_in'] = true;
                $_SESSION['user_id']        = $account['user_id'];
                $_SESSION['user_name']      = $account['first_name'] . ' ' . $account['last_name'];
                $_SESSION['user_type']      = 'intern';

                closeDBConnection($conn);
                header('Location: user_dashboard.php');
                exit;
            }
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
    <link rel="stylesheet" href="/css/Login_page.css">
    <title>Log In - OMH IT Timetracker</title>
    <style>
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