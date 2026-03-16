<?php
//user_dashboard.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

require_once 'db_config.php';

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login_page.php');
    exit;
}

// Auth check
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header('Location: Login_page.php');
    exit;
}

$user_id   = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

$conn = getDBConnection();

// Fetch required hours
$stmt = $conn->prepare("SELECT total_no_of_hrs_required FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user_data = $stmt->fetch();
$required_hours = $user_data ? (float)$user_data['total_no_of_hrs_required'] :

// Handle Clock In / Clock Out
$clock_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['clock_in'])) {
        $today = date('Y-m-d');
        $check = $conn->prepare("SELECT timelog_id FROM timelog WHERE user_id = ? AND curr_date = ? AND time_out IS NULL");
        $check->execute([$user_id, $today]);
        if (!$check->fetch()) {
            $ins = $conn->prepare("INSERT INTO timelog (user_id, curr_date, time_in) VALUES (?, ?, NOW())");
            $ins->execute([$user_id, $today]);
        }
        header('Location: user_dashboard.php');
        exit;
    }

    if (isset($_POST['clock_out'])) {
        $today = date('Y-m-d');
        $open = $conn->prepare("SELECT timelog_id, time_in FROM timelog WHERE user_id = ? AND curr_date = ? AND time_out IS NULL ORDER BY timelog_id DESC LIMIT 1");
        $open->execute([$user_id, $today]);
        $entry = $open->fetch();
        if ($entry) {
            $time_in_dt  = new DateTime($today . ' ' . $entry['time_in']);
            $time_out_dt = new DateTime();
            $diff_seconds = $time_out_dt->getTimestamp() - $time_in_dt->getTimestamp();
            $hours = round($diff_seconds / 3600, 2);

            $upd = $conn->prepare("UPDATE timelog SET time_out = NOW(), no_of_hrs_day = ? WHERE timelog_id = ?");
            $upd->execute([$hours, $entry['timelog_id']]);
        }
        header('Location: user_dashboard.php');
        exit;
    }
}

// Determine current clock state
$today = date('Y-m-d');
$open_check = $conn->prepare("SELECT timelog_id FROM timelog WHERE user_id = ? AND curr_date = ? AND time_out IS NULL");
$open_check->execute([$user_id, $today]);
$is_clocked_in = (bool)$open_check->fetch();

// Pagination
$rows_per_page = 15;
$page   = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $rows_per_page;

// Month / Year filter
$filter_month = isset($_GET['month']) ? intval($_GET['month']) : 0;
$filter_year  = isset($_GET['year'])  ? intval($_GET['year'])  : 0;

// Build dynamic WHERE clause
$where_parts = ["user_id = ?"];
$params      = [$user_id];

if ($filter_month > 0) {
    $where_parts[] = "MONTH(curr_date) = ?";
    $params[]      = $filter_month;
}
if ($filter_year > 0) {
    $where_parts[] = "YEAR(curr_date) = ?";
    $params[]      = $filter_year;
}

$where_clause = implode(" AND ", $where_parts);

// Fetch available years for dropdown
$year_stmt = $conn->prepare("SELECT DISTINCT YEAR(curr_date) as yr FROM timelog WHERE user_id = ? ORDER BY yr DESC");
$year_stmt->execute([$user_id]);
$available_years = $year_stmt->fetchAll(PDO::FETCH_COLUMN);

// Count total rows for pagination
$count_stmt = $conn->prepare("SELECT COUNT(*) FROM timelog WHERE $where_clause");
$count_stmt->execute($params);
$total_rows  = (int)$count_stmt->fetchColumn();
$total_pages = max(1, ceil($total_rows / $rows_per_page));

// Fetch rows
$logs_params = array_merge($params, [$rows_per_page, $offset]);
$logs_stmt = $conn->prepare("
    SELECT curr_date, time_in, time_out, no_of_hrs_day
    FROM timelog
    WHERE $where_clause
    ORDER BY curr_date ASC
    LIMIT ? OFFSET ?
");
$logs_stmt->execute($logs_params);
$logs = $logs_stmt->fetchAll();

// Total hours completed (all time)
$total_stmt = $conn->prepare("SELECT COALESCE(SUM(no_of_hrs_day), 0) FROM timelog WHERE user_id = ?");
$total_stmt->execute([$user_id]);
$total_hours = (float)$total_stmt->fetchColumn();

// Total days (all time, only completed)
$days_stmt = $conn->prepare("SELECT COUNT(*) FROM timelog WHERE user_id = ? AND time_out IS NOT NULL");
$days_stmt->execute([$user_id]);
$total_days = (int)$days_stmt->fetchColumn();

$remaining_hours = max(0, $required_hours - $total_hours);

closeDBConnection($conn);

// Month names for dropdown
$months = [
    1=>'January',  2=>'February', 3=>'March',    4=>'April',
    5=>'May',      6=>'June',     7=>'July',      8=>'August',
    9=>'September',10=>'October', 11=>'November', 12=>'December'
];

function fmt_time($t) {
    if (!$t) return '-';
    return date('h:i:s A', strtotime($t));
}
function fmt_date($d) {
    if (!$d) return '-';
    $days = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
    $dt   = new DateTime($d);
    return $dt->format('M d, Y') . ' (' . $days[$dt->format('w')] . ')';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/user_dashboard.css">
    <title>User Dashboard - OMH Cebu IT Timetracker</title>
</head>
<body>

<!-- HEADER -->
<div class="header">
    <div class="logo">
        <img src="/image/logo.png" alt="Omega Healthcare">
    </div>
    <div class="user-info">
        <div class="user-text">
            <p>Currently logged as: <?= htmlspecialchars($user_name) ?></p>
            <button class="logout-btn" onclick="window.location.href='?logout=1'">LOG OUT</button>
        </div>
        <div class="profile-icon">
            <img src="/image/profile_icon.png" alt="Profile">
        </div>
    </div>
</div>

<!-- CONTROLS -->
<div class="controls">
    <div class="date-time">Today is: <span id="datetime"></span></div>

    <div class="controls-right">

        <!-- Month / Year filter — auto-submits on change, no button needed -->
        <form class="month-select-form" method="get" id="filterForm">

            <!-- Month dropdown -->
            <select class="month-select" name="month" onchange="document.getElementById('filterForm').submit()">
                <option value="0" <?= $filter_month === 0 ? 'selected' : '' ?>>All Months</option>
                <?php foreach ($months as $num => $name): ?>
                    <option value="<?= $num ?>" <?= $num === $filter_month ? 'selected' : '' ?>>
                        <?= $name ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <!-- Year dropdown -->
            <select class="month-select" name="year" onchange="document.getElementById('filterForm').submit()">
                <option value="0" <?= $filter_year === 0 ? 'selected' : '' ?>>All Years</option>
                <?php foreach ($available_years as $yr): ?>
                    <option value="<?= $yr ?>" <?= intval($yr) === $filter_year ? 'selected' : '' ?>>
                        <?= $yr ?>
                    </option>
                <?php endforeach; ?>
            </select>

        </form>

        <!-- Clock In / Clock Out toggle -->
        <?php if (!$is_clocked_in): ?>
            <form method="post">
                <button type="submit" name="clock_in" class="clock-btn clock-in">CLOCK IN</button>
            </form>
        <?php else: ?>
            <button type="button" class="clock-btn clock-out" id="openClockOutModal">CLOCK OUT</button>
        <?php endif; ?>

    </div>
</div>

<!-- TABLE -->
<div class="table-wrapper">
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Time in</th>
                <th>Time out</th>
                <th>Total # of hours</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $filled = 0;
            foreach ($logs as $row):
                $filled++;
            ?>
            <tr>
                <td><?= fmt_date($row['curr_date']) ?></td>
                <td><?= fmt_time($row['time_in']) ?></td>
                <td><?= fmt_time($row['time_out']) ?></td>
                <td><?= $row['no_of_hrs_day'] !== null ? number_format($row['no_of_hrs_day'], 2) : '-' ?></td>
            </tr>
            <?php endforeach; ?>

            <?php for ($i = $filled; $i < $rows_per_page; $i++): ?>
            <tr class="empty-row">
                <td></td><td></td><td></td><td></td>
            </tr>
            <?php endfor; ?>
        </tbody>
    </table>
</div>

<!-- FOOTER ROW -->
<div class="footer-row">
    <div class="total-days">Total # of days &nbsp;&nbsp;: <?= $total_days ?> days</div>

    <div class="pagination">
        <?php $prev = $page - 1; $next = $page + 1; ?>
        <a class="page-btn <?= $page <= 1 ? 'disabled' : '' ?>"
           href="?page=<?= $prev ?>&month=<?= $filter_month ?>&year=<?= $filter_year ?>">&#8249;</a>
        <a class="page-btn <?= $page >= $total_pages ? 'disabled' : '' ?>"
           href="?page=<?= $next ?>&month=<?= $filter_month ?>&year=<?= $filter_year ?>">&#8250;</a>
    </div>

    <div class="hours-summary">
        <div>
            <span class="label">Hours completed: </span>
            <span class="value highlight"><?= number_format($total_hours, 2) ?> hrs.</span>
        </div>
        <div>
            <span class="label">Remaining hours: </span>
            <span class="value highlight"><?= number_format($remaining_hours, 2) ?> hrs.</span>
        </div>
        <div>
            <span class="label">Required hours: </span>
            <span class="value"><?= number_format($required_hours, 2) ?> hrs.</span>
        </div>
    </div>
</div>

<!-- CLOCK-OUT CONFIRMATION MODAL -->
<div class="modal-overlay" id="clockOutOverlay"></div>
<div class="modal-container" id="clockOutModal" style="display:none;">
    <div class="modal-content">
        <button class="modal-close" id="closeClockOutModal">←</button>
        <div class="warning-icon">⚠️</div>
        <div class="modal-message">
            Are you sure you want<br>to clock out?
        </div>
        <div class="modal-buttons">
            <form method="post">
                <div style="display:flex; gap:20px;">
                    <button type="submit" name="clock_out" class="yes-btn">YES</button>
                    <button type="button" class="no-btn" id="cancelClockOut">NO</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="/js/user_dashboard.js"></script>
</body>
</html>