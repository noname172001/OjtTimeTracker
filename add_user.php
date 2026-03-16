<?php
//add_user.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();


$admin_name = isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : 'Admin';


$db_host = 'localhost';
$db_name = 'OjtTimeTracker_db';
$db_user = 'root';
$db_pass = '1234';           


$success_message = '';
$error_message   = '';

// Handle form submission
if (isset($_POST['add_user'])) {
    $location    = trim($_POST['location']);
    $email       = trim($_POST['email']);
    $password    = $_POST['password'];
    $first_name  = trim($_POST['first_name']);
    $middle_name = trim($_POST['middle_name']);
    $last_name   = trim($_POST['last_name']);
    $school      = trim($_POST['school']);
    $address     = trim($_POST['address']);
    $mobile_no   = trim($_POST['mobile_no']);
    $total_hours = intval($_POST['total_hours']);

    if (empty($location) || empty($email) || empty($password) || empty($first_name) || empty($last_name) || empty($school) || $total_hours <= 0) {
        $error_message = 'All required fields must be filled and total hours must be greater than 0.';
    } else {
        require_once 'db_config.php';
        $conn = getDBConnection();

        // Check if email already exists in login table
        $check = $conn->prepare("SELECT login_id FROM login WHERE email = ?");
        $check->execute([$email]);

        if ($check->fetch()) {
            $error_message = 'A user with that email already exists.';
        } else {
            // Insert into users table first
            $stmt = $conn->prepare("
                INSERT INTO users 
                    (first_name, middle_name, last_name, school, total_no_of_hrs_required, location, address, mobile_no)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$first_name, $middle_name, $last_name, $school, $total_hours, $location, $address, $mobile_no]);

            $new_user_id = $conn->lastInsertId();

            // Insert into login table
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $login_stmt = $conn->prepare("
                INSERT INTO login (user_id, email, password, role)
                VALUES (?, ?, ?, 'intern')
            ");
            $login_stmt->execute([$new_user_id, $email, $hashed_password]);

            closeDBConnection($conn);
            header('Location: admin_dashboard.php');
            exit;
        }

        closeDBConnection($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/add_user.css">
    <title>Add User - OMH Cebu IT Timetracker</title>
    <style>
    </style>
</head>
<body>
    <!-- Blurred background content -->
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
    
    <!-- Add User Modal -->
    <div class="modal-container">
        <div class="modal-content">
            <a href="admin_dashboard.php" class="back-arrow">←</a>
            <div class="modal-title">ADD USER</div>

            <?php if (!empty($error_message)): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>

            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
            <?php endif; ?>

<form method="POST">
    <div class="form-group">
        <label>Location:</label>
        <select name="location" required>
            <option value="">Select</option>
            <option value="Cebu"   <?php echo (isset($_POST['location']) && $_POST['location'] === 'Cebu')   ? 'selected' : ''; ?>>Cebu</option>
            <option value="Manila" <?php echo (isset($_POST['location']) && $_POST['location'] === 'Manila') ? 'selected' : ''; ?>>Manila</option>
        </select>
    </div>
    <div class="form-group">
        <label>Email:</label>
        <input type="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
    </div>
    <div class="form-group">
        <label>Password:</label>
        <input type="password" name="password" required>
    </div>
    <div class="form-group">
        <label>First Name:</label>
        <input type="text" name="first_name" value="<?php echo isset($_POST['first_name']) ? htmlspecialchars($_POST['first_name']) : ''; ?>" required>
    </div>
    <div class="form-group">
        <label>Middle Name:</label>
        <input type="text" name="middle_name" value="<?php echo isset($_POST['middle_name']) ? htmlspecialchars($_POST['middle_name']) : ''; ?>">
    </div>
    <div class="form-group">
        <label>Last Name:</label>
        <input type="text" name="last_name" value="<?php echo isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name']) : ''; ?>" required>
    </div>
    <div class="form-group">
        <label>School:</label>
        <input type="text" name="school" value="<?php echo isset($_POST['school']) ? htmlspecialchars($_POST['school']) : ''; ?>" required>
    </div>
    <div class="form-group">
        <label>Address:</label>
        <input type="text" name="address" value="<?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?>">
    </div>
    <div class="form-group">
        <label>Mobile No:</label>
        <input type="text" name="mobile_no" value="<?php echo isset($_POST['mobile_no']) ? htmlspecialchars($_POST['mobile_no']) : ''; ?>">
    </div>
    <div class="form-group">
        <label>Total hours required:</label>
        <input type="number" name="total_hours" value="<?php echo isset($_POST['total_hours']) ? htmlspecialchars($_POST['total_hours']) : ''; ?>" min="1" required>
    </div>
    <button type="submit" name="add_user" class="add-btn">ADD</button>
</form>
        </div>
    </div>
    
    <script src="/js/add_user.js"></script>
</body>
</html>


