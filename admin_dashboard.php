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
               u.total_no_of_hrs_required AS hours, u.location
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
    <link rel="stylesheet" href="/css/admin_dashboard.css">
    <title>Admin Dashboard - OMH Cebu IT Timetracker</title>
    <style>
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
                <th>Location</th>
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
                    <td><?php echo html_entity_decode($intern['location']); ?></td>
                    <td>
                        <a href="delete_confirmation.php?id=<?php echo $intern['id']; ?>&name=<?php echo urlencode($intern['name']); ?>">
                            <img src="/image/delete_icon.png" alt="Delete" class="delete-icon">
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="no-results">
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
    
    <script src="/js/admin_dashboard.js"></script>
</body>
</html>
