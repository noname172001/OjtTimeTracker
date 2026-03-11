<?php
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
    $name        = trim($_POST['name']);
    $school      = trim($_POST['school']);
    $total_hours = intval($_POST['total_hours']);

    // Basic validation
    if (empty($location) || empty($email) || empty($password) || empty($name) || empty($school) || $total_hours <= 0) {
        $error_message = 'All fields are required and total hours must be greater than 0.';
    } else {
        // Connect to MySQL
        $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

        if ($conn->connect_error) {
            $error_message = 'Database connection failed: ' . $conn->connect_error;
        } else {
            // Check if email already exists
            $check_stmt = $conn->prepare("SELECT user_id FROM users WHERE user_email = ?");
            $check_stmt->bind_param('s', $email);
            $check_stmt->execute();
            $check_stmt->store_result();

            if ($check_stmt->num_rows > 0) {
                $error_message = 'A user with that email already exists.';
            } else {
                // Hash the password before storing
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $conn->prepare(
                    "INSERT INTO users 
                        (user_name, user_email, user_password, user_location, user_school, user_total_hours_required)
                     VALUES (?, ?, ?, ?, ?, ?)"
                );
                $stmt->bind_param('sssssi', $name, $email, $hashed_password, $location, $school, $total_hours);

                if ($stmt->execute()) {
                    // Redirect on success
                    header('Location: admin_dashboard.php');
                    exit;
                } else {
                    $error_message = 'Failed to add user: ' . $stmt->error;
                }

                $stmt->close();
            }

            $check_stmt->close();
            $conn->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User - OMH Cebu IT Timetracker</title>
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
            padding: 30px 40px;
            border: 3px solid #00ff00;
            border-radius: 12px;
            min-width: 450px;
            position: relative;
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
        
        .modal-title {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
            margin-top: 15px;
        }

        .alert {
            padding: 10px 14px;
            border-radius: 6px;
            margin-bottom: 16px;
            font-size: 14px;
            font-weight: bold;
            text-align: center;
        }

        .alert-error {
            background: #ffe0e0;
            border: 1px solid #ff4444;
            color: #cc0000;
        }

        .alert-success {
            background: #e0ffe0;
            border: 1px solid #00cc00;
            color: #006600;
        }
        
        .form-group {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .form-group label {
            width: 180px;
            font-size: 16px;
            font-weight: bold;
            text-align: left;
        }
        
        .form-group input,
        .form-group select {
            flex: 1;
            padding: 8px 10px;
            border: 1px solid #333;
            font-size: 14px;
            background: white;
        }
        
        .form-group select {
            background: #00ff00;
            font-weight: bold;
        }
        
        .add-btn {
            display: block;
            margin: 30px auto 0;
            padding: 10px 40px;
            background: #00ff00;
            border: 1px solid #000;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            border-radius: 4px;
        }
        
        .add-btn:hover {
            background: #00dd00;
        }
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
                    <label>Name:</label>
                    <input type="text" name="name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label>School:</label>
                    <input type="text" name="school" value="<?php echo isset($_POST['school']) ? htmlspecialchars($_POST['school']) : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label>Total hours required:</label>
                    <input type="number" name="total_hours" value="<?php echo isset($_POST['total_hours']) ? htmlspecialchars($_POST['total_hours']) : ''; ?>" min="1" required>
                </div>
                <button type="submit" name="add_user" class="add-btn">ADD</button>
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