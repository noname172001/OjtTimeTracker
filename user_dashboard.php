<?php
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
    header('Location: index.php');
    exit;
}

$user_id   = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

$conn = getDBConnection();

// Fetch required hours
$stmt = $conn->prepare("SELECT total_no_of_hrs_required FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user_data = $stmt->fetch();
$required_hours = $user_data ? (float)$user_data['total_no_of_hrs_required'] : 729;

// Handle Clock In / Clock Out
$clock_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['clock_in'])) {
        // Check no open entry today
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

// Pagination - 5 rows per page
$rows_per_page = 5;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $rows_per_page;

// Month filter
$filter_month = isset($_GET['month']) ? intval($_GET['month']) : intval(date('m'));
$filter_year  = isset($_GET['year'])  ? intval($_GET['year'])  : intval(date('Y'));

// Count total rows for pagination
$count_stmt = $conn->prepare("SELECT COUNT(*) FROM timelog WHERE user_id = ? AND MONTH(curr_date) = ? AND YEAR(curr_date) = ?");
$count_stmt->execute([$user_id, $filter_month, $filter_year]);
$total_rows  = (int)$count_stmt->fetchColumn();
$total_pages = max(1, ceil($total_rows / $rows_per_page));

// Fetch rows
$logs_stmt = $conn->prepare("
    SELECT curr_date, time_in, time_out, no_of_hrs_day
    FROM timelog
    WHERE user_id = ? AND MONTH(curr_date) = ? AND YEAR(curr_date) = ?
    ORDER BY curr_date ASC
    LIMIT ? OFFSET ?
");
$logs_stmt->execute([$user_id, $filter_month, $filter_year, $rows_per_page, $offset]);
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
    1=>'January',2=>'February',3=>'March',4=>'April',
    5=>'May',6=>'June',7=>'July',8=>'August',
    9=>'September',10=>'October',11=>'November',12=>'December'
];

function fmt_time($t) {
    if (!$t) return '-';
    return date('h:i:s A', strtotime($t));
}
function fmt_date($d) {
    if (!$d) return '-';
    $days = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
    $dt = new DateTime($d);
    return $dt->format('M d, Y') . ' (' . $days[$dt->format('w')] . ')';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - OMH Cebu IT Timetracker</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Arial, sans-serif;
            background: #b0b0b0;
            padding: 20px 30px;
            min-height: 100vh;
        }

        /* ── HEADER ── */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 18px;
        }
        .logo img { width: 160px; }
        .user-info {
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .user-text { text-align: right; }
        .user-text p { font-size: 13px; color: #222; margin-bottom: 5px; }
        .logout-form { display: inline; }
        .logout-btn {
            padding: 5px 16px;
            background: #00ff00;
            border: 1px solid #000;
            font-size: 12px;
            font-weight: bold;
            border-radius: 3px;
            cursor: pointer;
        }
        .logout-btn:hover { background: #00dd00; }
        .profile-icon {
            width: 56px; height: 56px;
            background: white;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            border: 2px solid #333;
            overflow: hidden;
        }
        .profile-icon img { width: 36px; height: 36px; }

        /* ── CONTROLS BAR ── */
        .controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 14px;
        }
        .date-time { font-size: 17px; font-weight: bold; color: #222; }

        .controls-right { display: flex; gap: 10px; align-items: center; }

        .month-select-form { display: flex; gap: 0; }
        .month-select {
            padding: 6px 10px;
            font-size: 13px;
            border: 1px solid #555;
            border-right: none;
            background: #e8e8e8;
            cursor: pointer;
            border-radius: 3px 0 0 3px;
        }
        .month-select-btn {
            padding: 6px 14px;
            background: #00c800;
            border: 1px solid #555;
            font-size: 13px;
            font-weight: bold;
            cursor: pointer;
            border-radius: 0 3px 3px 0;
        }
        .month-select-btn:hover { background: #00aa00; }

        .clock-btn {
            padding: 6px 20px;
            border: 1px solid #333;
            font-size: 13px;
            font-weight: bold;
            border-radius: 3px;
            cursor: pointer;
        }
        .clock-btn.clock-in  { background: #00ff00; }
        .clock-btn.clock-in:hover  { background: #00dd00; }
        .clock-btn.clock-out { background: #ff4444; color: white; }
        .clock-btn.clock-out:hover { background: #cc0000; }

        /* ── TABLE ── */
        .table-wrapper {
            background: white;
            border: 1px solid #999;
        }
        table { width: 100%; border-collapse: collapse; }
        th {
            background: #00ff00;
            padding: 11px 8px;
            text-align: center;
            font-size: 16px;
            border: 1px solid #555;
            font-weight: bold;
        }
        td {
            padding: 9px 8px;
            text-align: center;
            font-size: 14px;
            border: 1px solid #ccc;
            color: #222;
        }
        tr:nth-child(even) td { background: #f7f7f7; }

        /* Empty rows */
        .empty-row td { height: 38px; color: #bbb; }

        /* ── FOOTER ROW ── */
        .footer-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 12px;
        }
        .total-days { font-size: 15px; font-weight: bold; color: #222; }

        /* Pagination */
        .pagination { display: flex; gap: 6px; align-items: center; }
        .page-btn {
            width: 32px; height: 32px;
            border-radius: 50%;
            border: 1px solid #333;
            background: #555;
            color: white;
            font-size: 18px;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            text-decoration: none;
            font-weight: bold;
        }
        .page-btn:hover { background: #333; }
        .page-btn.disabled { background: #aaa; pointer-events: none; }

        /* Hours summary */
        .hours-summary { text-align: right; font-size: 14px; color: #222; line-height: 1.8; }
        .hours-summary .label  { display: inline-block; min-width: 140px; }
        .hours-summary .value  { font-weight: bold; }
        .hours-summary .highlight { font-size: 15px; font-weight: bold; }

        /* ══════════════════════════════
           CLOCK-OUT CONFIRMATION MODAL
        ══════════════════════════════ */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.35);
            z-index: 999;
        }
        .modal-overlay.active { display: block; }

        .modal-container {
            position: fixed;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1000;
        }
        .modal-content {
            background: #fefefe;
            padding: 40px 50px;
            border: 3px solid #00ff00;
            border-radius: 12px;
            min-width: 400px;
            position: relative;
            text-align: center;
        }
        .modal-close {
            position: absolute;
            top: 13px; left: 15px;
            font-size: 30px;
            font-weight: bold;
            color: #333;
            cursor: pointer;
            background: none;
            border: none;
            line-height: 1;
        }
        .modal-close:hover { color: #000; }
        .warning-icon { font-size: 48px; margin-bottom: 12px; }
        .modal-message {
            font-size: 18px;
            color: #333;
            line-height: 1.6;
            margin-bottom: 24px;
        }
        .modal-buttons { display: flex; gap: 20px; justify-content: center; }
        .yes-btn {
            padding: 10px 35px;
            background: #00ff00;
            border: 1px solid #000;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            border-radius: 4px;
        }
        .yes-btn:hover { background: #00dd00; }
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
        .no-btn:hover { background: #dd0000; }
    </style>
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
        <!-- Month filter -->
        <form class="month-select-form" method="get">
            <select class="month-select" name="month">
                <?php foreach ($months as $num => $name): ?>
                    <option value="<?= $num ?>" <?= $num === $filter_month ? 'selected' : '' ?>>
                        <?= $name ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="hidden" name="year" value="<?= $filter_year ?>">
            <button class="month-select-btn" type="submit">▼ SELECT MONTH</button>
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


<!-- ══ CLOCK-OUT CONFIRMATION MODAL ══ -->
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


<script>
    // Live clock
    function updateDateTime() {
        const now = new Date();
        const pad = n => String(n).padStart(2,'0');
        const mm = pad(now.getMonth()+1), dd = pad(now.getDate()), yy = now.getFullYear();
        const hh = pad(now.getHours()), mi = pad(now.getMinutes()), ss = pad(now.getSeconds());
        document.getElementById('datetime').textContent = `${mm}/${dd}/${yy} ${hh}:${mi}:${ss}`;
    }
    updateDateTime();
    setInterval(updateDateTime, 1000);

    // Modal logic
    const overlay = document.getElementById('clockOutOverlay');
    const modal   = document.getElementById('clockOutModal');
    function openModal()  { overlay.classList.add('active'); modal.style.display = 'block'; }
    function closeModal() { overlay.classList.remove('active'); modal.style.display = 'none'; }

    const openBtn   = document.getElementById('openClockOutModal');
    const closeBtn  = document.getElementById('closeClockOutModal');
    const cancelBtn = document.getElementById('cancelClockOut');

    if (openBtn)   openBtn.addEventListener('click', openModal);
    if (closeBtn)  closeBtn.addEventListener('click', closeModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
    overlay.addEventListener('click', closeModal);
</script>
</body>
</html>