<?php
//admin_dashboard.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

require_once 'db_config.php';

$admin_name = isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : 'Admin';

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login_page.php');
    exit;
}

// Handle search
$search_query = '';
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';

// Fetch interns from database
$conn = getDBConnection();

if (!empty($search_query)) {
    $stmt = $conn->prepare("
        SELECT u.user_id AS id, 
               CONCAT(u.first_name, ' ', IFNULL(u.middle_name, ''), ' ', u.last_name) AS name,
               u.total_no_of_hrs_required AS hours
        FROM users u
        JOIN login l ON u.user_id = l.user_id
        WHERE l.role = 'intern'
        AND (
            CONCAT(u.first_name, ' ', u.last_name) LIKE :search 
            OR u.user_id LIKE :search2
        )
    ");
    $stmt->execute([
        'search'  => '%' . $search_query . '%',
        'search2' => '%' . $search_query . '%',
    ]);
} else {
    $stmt = $conn->query("
        SELECT u.user_id AS id, 
               CONCAT(u.first_name, ' ', IFNULL(u.middle_name, ''), ' ', u.last_name) AS name,
               u.total_no_of_hrs_required AS hours
        FROM users u
        JOIN login l ON u.user_id = l.user_id
        WHERE l.role = 'intern'
    ");
}

$interns = $stmt->fetchAll();
$total_interns = count($interns);

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - OMH Cebu IT Timetracker</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background: #aaa;
            padding: 20px;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .logo img {
            width: 180px;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
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
            cursor: pointer;
            font-size: 13px;
            font-weight: bold;
            border-radius: 3px;
        }
        
        .logout-btn:hover {
            background: #00dd00;
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
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .date-time {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }
        
        .search-add {
            display: flex;
            gap: 15px;
            align-items: center;
        }
        
        .search-box {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .search-box label {
            font-size: 16px;
            color: #333;
        }
        
        .search-box input {
            padding: 6px 10px;
            border: 1px solid #333;
            font-size: 14px;
            width: 200px;
        }
        
        .search-btn {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 18px;
            padding: 5px;
        }
        
        .add-user-btn {
            padding: 8px 20px;
            background: #00ff00;
            border: 1px solid #000;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            border-radius: 3px;
            text-decoration: none;
            color: #000;
            display: inline-block;
        }
        
        .add-user-btn:hover {
            background: #00dd00;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            margin-bottom: 15px;
        }
        
        th {
            background: #00ff00;
            padding: 12px;
            text-align: center;
            font-size: 18px;
            border: 1px solid #333;
            font-weight: bold;
        }
        
        td {
            padding: 12px;
            text-align: center;
            border: 1px solid #333;
            font-size: 16px;
        }
        
        tr:nth-child(even) {
            background: #f9f9f9;
        }
        
        .delete-icon {
            cursor: pointer;
            width: 24px;
            height: 24px;
        }
        
        .total-count {
            font-size: 16px;
            font-weight: bold;
            color: #333;
        }

        .no-results {
            text-align: center;
            padding: 20px;
            color: #666;
            font-style: italic;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="logo">
            <img src="/image/logo.png" alt="Omega Healthcare">
        </div>
        <div class="user-info">
            <div class="user-text">
                <p>Currently logged as: <?php echo htmlspecialchars($admin_name); ?></p>
                <button class="logout-btn" onclick="window.location.href='?logout=1'">LOG OUT</button>
            </div>
            <div class="profile-icon">
                <img src="/image/profile_icon.png" alt="Profile">
            </div>
        </div>
    </div>
    
    <!-- Controls -->
    <div class="controls">
        <div class="date-time">
            Today is: <span id="datetime"></span>
        </div>
        <div class="search-add">
            <form class="search-box" method="GET">
                <label>Search user:</label>
                <input type="text" name="search" value="<?php echo htmlspecialchars($search_query); ?>" placeholder="">
                <button type="submit" class="search-btn">🔍</button>
            </form>
            <a href="add_user.php" class="add-user-btn">ADD USER</a>
        </div>
    </div>
    
    <!-- Table -->
    <table>
        <thead>
            <tr>
                <th>INTERN ID #</th>
                <th>Name</th>
                <th>Total # of hours</th>
                <th>Delete</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($interns)): ?>
                <?php foreach ($interns as $intern): ?>
                <tr>
                    <td><?php echo htmlspecialchars($intern['id']); ?></td>
                    <td><?php echo htmlspecialchars($intern['name']); ?></td>
                    <td><?php echo htmlspecialchars($intern['hours']); ?> hrs</td>
                    <td>
                        <a href="delete_confirmation.php?id=<?php echo $intern['id']; ?>&name=<?php echo urlencode($intern['name']); ?>">
                            <img src="/image/delete_icon.png" alt="Delete" class="delete-icon">
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="no-results">
                        <?php echo !empty($search_query) ? 'No interns found matching "' . htmlspecialchars($search_query) . '"' : 'No interns found.'; ?>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    
    <!-- Total count -->
    <div class="total-count">
        Total # of Interns : <?php echo $total_interns; ?> Interns
    </div>
    
    <script>
        function updateDateTime() {
            const now = new Date();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const day = String(now.getDate()).padStart(2, '0');
            const year = now.getFullYear();
            const hours = String(now.getHours()).padStart(2, '0');
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