<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OMH | OJT Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --accent: #42bff5;
            --bg-body: #f8fafc;
            --sidebar-bg: #ffffff;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --glass: rgba(255, 255, 255, 0.8);
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-body);
            margin: 0;
            display: flex;
            height: 100vh;
            color: var(--text-main);
        }

        /* --- Left Sidebar --- */
        aside {
            width: 280px;
            background: var(--sidebar-bg);
            border-right: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 40px;
            z-index: 10;
        }

        .profile-container {
            text-align: center;
            margin-bottom: 40px;
        }

        .profile-img {
            width: 1.2in; /* Requested Size */
            height: 1.2in;
            border-radius: 16px;
            object-fit: cover;
            border: 3px solid var(--accent);
            box-shadow: 0 10px 15px -3px rgba(66, 191, 245, 0.2);
            margin-bottom: 15px;
        }

        .nav-menu {
            width: 100%;
            padding: 0 20px;
            box-sizing: border-box;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 18px;
            text-decoration: none;
            color: var(--text-muted);
            font-weight: 500;
            border-radius: 10px;
            margin-bottom: 8px;
            transition: all 0.3s ease;
        }

        .nav-link:hover, .nav-link.active {
            background-color: #f0f9ff;
            color: var(--accent);
        }

        .nav-link.active {
            background-color: var(--accent);
            color: white;
        }

        /* --- Main Content Area --- */
        .main-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* --- Top Header --- */
        header {
            height: 70px;
            background: var(--glass);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: flex-end; /* Right aligned nav */
            padding: 0 40px;
        }

        .header-user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-name {
            font-weight: 600;
            font-size: 0.95rem;
        }

        .btn-logout {
            padding: 8px 16px;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            color: #ef4444; /* Red for logout */
            transition: 0.2s;
        }

        .btn-logout:hover {
            background: #fef2f2;
            border-color: #fecaca;
        }

        /* --- Dashboard Content --- */
        main {
            padding: 40px;
            overflow-y: auto;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            border-left: 5px solid var(--accent);
        }

        .stat-card h4 {
            margin: 0;
            color: var(--text-muted);
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-card p {
            margin: 10px 0 0;
            font-size: 1.8rem;
            font-weight: 700;
        }

        .table-container {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            padding: 15px;
            border-bottom: 2px solid #f1f5f9;
            color: var(--text-muted);
            font-size: 0.85rem;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.9rem;
        }

        .status-tag {
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            background: #ecfdf5;
            color: #10b981;
        }

    </style>
</head>
<body>

    <aside>
        <div class="profile-container">
            <img src="https://ui-avatars.com/api/?name=Juan+Dela+Cruz&background=42bff5&color=fff&size=200" alt="Profile" class="profile-img">
            <div style="font-weight: 700; font-size: 1.1rem;">Juan Dela Cruz</div>
            <div style="color: var(--text-muted); font-size: 0.8rem;">IT Intern | Batch 2026</div>
        </div>

        <nav class="nav-menu">
            <a href="#" class="nav-link active">Dashboard</a>
            <a href="#" class="nav-link">My Timesheets</a>
            <a href="#" class="nav-link">View Profile</a>
            <a href="#" class="nav-link">Settings</a>
            <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">
            <a href="#" class="nav-link" style="color: #ef4444;">Logout</a>
        </nav>
    </aside>

    <div class="main-wrapper">
        <header>
            <div class="header-user-info">
                <span class="user-name">Juan Dela Cruz</span>
                <button class="btn-logout">Logout</button>
            </div>
        </header>

        <main>
            <h2 style="margin-bottom: 30px;">OJT Overview</h2>

            <div class="stats-grid">
                <div class="stat-card">
                    <h4>Total Hours</h4>
                    <p>120 / 480</p>
                </div>
                <div class="stat-card">
                    <h4>Days Remaining</h4>
                    <p>45 Days</p>
                </div>
                <div class="stat-card">
                    <h4>Attendance Rate</h4>
                    <p>98%</p>
                </div>
            </div>

            <div class="table-container">
                <h3 style="margin-top: 0; margin-bottom: 20px;">Recent Logs</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time In</th>
                            <th>Time Out</th>
                            <th>Hours</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>March 13, 2026</td>
                            <td>08:00 AM</td>
                            <td>05:00 PM</td>
                            <td>9.00</td>
                            <td><span class="status-tag">Verified</span></td>
                        </tr>
                        <tr>
                            <td>March 12, 2026</td>
                            <td>07:55 AM</td>
                            <td>05:05 PM</td>
                            <td>9.10</td>
                            <td><span class="status-tag">Verified</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

</body>
</html>