<?php
//delete_confirmation.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

require_once 'db_config.php';

$admin_name = isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : 'Admin';

// Get user info from URL
$user_id   = isset($_GET['id'])   ? intval($_GET['id'])              : 0;
$user_name = isset($_GET['name']) ? htmlspecialchars($_GET['name'])  : '';

$error_message = '';

// Handle delete confirmation
if (isset($_POST['confirm_delete'])) {
    $delete_id = intval($_POST['user_id']);

    if ($delete_id <= 0) {
        $error_message = 'Invalid user ID.';
    } else {
        $conn = getDBConnection();

        // Also delete related user_logs to avoid orphaned records
        $stmt_logs = $conn->prepare("DELETE FROM user_logs WHERE user_id = :user_id");
        $stmt_logs->execute([':user_id' => $delete_id]);

        $stmt = $conn->prepare("DELETE FROM users WHERE user_id = :user_id");

        if ($stmt->execute([':user_id' => $delete_id])) {
            closeDBConnection($conn);
            header('Location: admin_dashboard.php');
            exit;
        } else {
            $error_message = 'Failed to delete user. Please try again.';
            closeDBConnection($conn);
        }
    }
}

// Handle cancel
if (isset($_POST['cancel_delete'])) {
    header('Location: admin_dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Confirmation - OMH Cebu IT Timetracker</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background: rgba(200, 200, 200, 0.7);
            padding: 20px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .logo img {
            width: 180px;
            opacity: 0.5;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
            opacity: 0.5;
        }
        
        .user-text {
            text-align: right;
        }
        
        .user-text p {
            margin-bottom: 5px;
            font-size: 14px;
            color: #333;
        }
        
        .logout-btn {
            padding: 6px 18px;
            background: #00ff00;
            border: 1px solid #000;
            font-size: 13px;
            font-weight: bold;
            border-radius: 3px;
        }
        
        .profile-icon {
            width: 60px;
            height: 60px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #333;
        }
        
        .profile-icon img {
            width: 40px;
            height: 40px;
        }
        
        .controls {
            opacity: 0.5;
            margin-bottom: 20px;
        }
        
        .date-time {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            margin-bottom: 15px;
            opacity: 0.5;
        }
        
        th {
            background: #00ff00;
            padding: 12px;
            text-align: center;
            font-size: 18px;
            border: 1px solid #333;
            font-weight: bold;
        }
        
        .modal-container {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1000;
        }
        
        .modal-content {
            background-color: #fefefe;
            padding: 40px;
            border: 3px solid #00ff00;
            border-radius: 12px;
            min-width: 400px;
            position: relative;
            text-align: center;
        }
        
        .back-arrow {
            position: absolute;
            top: 15px;
            left: 15px;
            font-size: 32px;
            font-weight: bold;
            text-decoration: none;
            color: #333;
            cursor: pointer;
        }
        
        .back-arrow:hover {
            color: #000;
        }
        
        .warning-icon {
            font-size: 48px;
            color: #ff9900;
            margin-bottom: 15px;
        }
        
        .delete-message {
            font-size: 18px;
            margin-bottom: 10px;
            color: #333;
            line-height: 1.5;
        }

        .delete-name {
            font-size: 16px;
            font-weight: bold;
            color: #cc0000;
            margin-bottom: 20px;
        }

        .alert-error {
            background: #ffe0e0;
            border: 1px solid #ff4444;
            color: #cc0000;
            padding: 10px 14px;
            border-radius: 6px;
            margin-bottom: 16px;
            font-size: 14px;
            font-weight: bold;
        }
        
        .modal-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
        }
        
        .yes-btn {
            padding: 10px 35px;
            background: #00ff00;
            border: 1px solid #000;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            border-radius: 4px;
        }
        
        .yes-btn:hover {
            background: #00dd00;
        }
        
        .no-btn {
            padding: 10px 35px;
            background: #ff0000;
            border: 1px solid #000;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            border-radius: 4px;
            color: white;
        }
        
        .no-btn:hover {
            background: #dd0000;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">
            <img src="/image/logo.png" alt="Omega Healthcare">
        </div>
        <div class="user-info">
            <div class="user-text">
                <p>Currently logged as: <?php echo htmlspecialchars($admin_name); ?></p>
                <button class="logout-btn">LOG OUT</button>
            </div>
            <div class="profile-icon">
                <img src="/image/profile.png" alt="Profile">
            </div>
        </div>
    </div>
    
    <div class="controls">
        <div class="date-time">
            Today is: <span id="datetime"></span>
        </div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>INTERN ID #</th>
                <th>Name</th>
                <th>Total # of hours</th>
                <th>Delete</th>
            </tr>
        </thead>
    </table>
    
    <!-- Delete Confirmation Modal -->
    <div class="modal-container">
        <div class="modal-content">
            <a href="admin_dashboard.php" class="back-arrow">←</a>
            <div class="warning-icon">⚠️</div>

            <?php if (!empty($error_message)): ?>
                <div class="alert-error"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <div class="delete-message">
                Are you sure you want<br>to delete this user?
            </div>

            <?php if (!empty($user_name)): ?>
                <div class="delete-name"><?php echo $user_name; ?></div>
            <?php endif; ?>

            <form method="POST">
                <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                <div class="modal-buttons">
                    <button type="submit" name="confirm_delete" class="yes-btn">YES</button>
                    <button type="submit" name="cancel_delete" class="no-btn">NO</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        function updateDateTime() {
            const now = new Date();
            const month   = String(now.getMonth() + 1).padStart(2, '0');
            const day     = String(now.getDate()).padStart(2, '0');
            const year    = now.getFullYear();
            const hours   = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            document.getElementById('datetime').textContent =
                `${month}/${day}/${year} ${hours}:${minutes}:${seconds}`;
        }
        updateDateTime();
        setInterval(updateDateTime, 1000);
    </script>
</body>
</html>