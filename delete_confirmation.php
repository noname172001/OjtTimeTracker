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
    <link rel="stylesheet" href="/css/delete_confirmation.css">
    <title>Delete Confirmation - OMH Cebu IT Timetracker</title>
    <style>
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
    
    <script scr ="/js/delete_confirmation.js"></script>
</body>
</html>