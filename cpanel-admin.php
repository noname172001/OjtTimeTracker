<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OMH Admin | Intern Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        :root {
            --accent: #42bff5;
            --bg-body: #f1f5f9;
            --sidebar-bg: #ffffff;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --glass: rgba(255, 255, 255, 0.85);
            --danger: #ff4757;
            --success: #2ed573;
            --border: #e2e8f0;
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
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            padding-top: 30px;
            z-index: 10;
        }

        .admin-badge {
            align-self: center;
            background: #e0f4ff;
            color: var(--accent);
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            margin-bottom: 20px;
        }

        .profile-container {
            text-align: center;
            margin-bottom: 30px;
        }

        .profile-img {
            width: 1.2in;
            height: 1.2in;
            border-radius: 20px;
            object-fit: cover;
            border: 3px solid var(--accent);
            box-shadow: 0 10px 20px rgba(66, 191, 245, 0.15);
            margin-bottom: 15px;
        }

        .nav-menu {
            width: 100%;
            padding: 0 20px;
            box-sizing: border-box;
            flex-grow: 1;
        }

        .nav-label {
            font-size: 0.7rem;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            margin: 20px 0 10px 15px;
            letter-spacing: 1px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 18px;
            text-decoration: none;
            color: var(--text-muted);
            font-weight: 500;
            border-radius: 12px;
            margin-bottom: 5px;
            transition: all 0.2s ease;
        }

        .nav-link:hover,
        .nav-link.active {
            background-color: #f0f9ff;
            color: var(--accent);
        }

        .nav-link.active {
            background-color: var(--accent);
            color: white;
            box-shadow: 0 4px 12px rgba(66, 191, 245, 0.3);
        }

        .nav-link.logout {
            color: var(--danger);
            margin-top: 20px;
            margin-bottom: 30px;
        }

        .nav-link.logout:hover {
            background-color: #fff1f2;
            color: var(--danger);
        }

        /* --- Main Content --- */
        .main-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        header {
            height: 70px;
            background: var(--glass);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 40px;
        }

        .search-bar {
            background: #f8fafc;
            border: 1px solid var(--border);
            padding: 8px 20px;
            border-radius: 10px;
            width: 300px;
            outline: none;
            font-family: inherit;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        /* --- Dashboard Content --- */
        main {
            padding: 35px 40px;
            overflow-y: auto;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .btn-primary {
            background: var(--accent);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(66, 191, 245, 0.3);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(66, 191, 245, 0.4);
        }

        .admin-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 35px;
        }

        .metric-card {
            background: white;
            padding: 20px;
            border-radius: 18px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
        }

        .metric-card h4 {
            margin: 0;
            color: var(--text-muted);
            font-size: 0.8rem;
        }

        .metric-card p {
            margin: 8px 0 0;
            font-size: 1.5rem;
            font-weight: 700;
        }

        .data-panel {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
        }

        .panel-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            padding: 15px;
            border-bottom: 1px solid #f1f5f9;
            color: var(--text-muted);
            font-size: 0.8rem;
            text-transform: uppercase;
        }

        td {
            padding: 18px 15px;
            border-bottom: 1px solid #f8fafc;
            font-size: 0.9rem;
        }

        .user-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-avatar-mini {
            width: 35px;
            height: 35px;
            border-radius: 8px;
            background: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .progress-bar-bg {
            width: 100px;
            height: 8px;
            background: #e2e8f0;
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: var(--accent);
        }

        /* --- Updated Action Buttons --- */
        .btn-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 8px 14px;
            border-radius: 8px;
            border: 1px solid var(--border);
            background: white;
            cursor: pointer;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-main);
            transition: all 0.2s;
            margin-right: 4px;
        }

        .btn-action svg {
            width: 14px;
            height: 14px;
            stroke-width: 2.5px;
        }

        .btn-action:hover {
            border-color: var(--accent);
            color: var(--accent);
            background: #f0f9ff;
        }

        .btn-outline-danger {
            border: 1px solid #fee2e2;
            color: var(--danger);
            background: transparent;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
        }

        .btn-outline-danger:hover {
            background: #fff1f2;
        }

        /* --- Filter Section --- */
        .panel-filters {
            display: flex;
            gap: 12px;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #f1f5f9;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .filter-label {
            font-size: 0.65rem;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-left: 4px;
        }

        .select-custom {
            background: #f8fafc;
            border: 1px solid var(--border);
            padding: 8px 12px;
            border-radius: 10px;
            font-size: 0.85rem;
            color: var(--text-main);
            outline: none;
            min-width: 160px;
            cursor: pointer;
            transition: all 0.2s;
            font-family: inherit;
            appearance: none;
            /* Removes default browser arrow */
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
            padding-right: 35px;
        }

        .select-custom:focus {
            border-color: var(--accent);
            background-color: #ffffff;
            box-shadow: 0 0 0 3px rgba(66, 191, 245, 0.1);
        }
    </style>
</head>

<body>

    <aside>
        <div class="admin-badge">ADMINISTRATION PANEL</div>
        <div class="profile-container">
            <img src="https://ui-avatars.com/api/?name=Admin+User&background=1e293b&color=fff" class="profile-img">
            <div style="font-weight: 700; font-size: 1.1rem;">Aldin Moreno</div>
            <div style="color: var(--text-muted); font-size: 0.8rem;">Senior IT Engineer</div>
        </div>

        <nav class="nav-menu">
            <div class="nav-label">Management</div>
            <a href="#" class="nav-link active">Intern Directory</a>
            <a href="#" class="nav-link">Attendance Review</a>
            <a href="#" class="nav-link">Project Assignments</a>

            <div class="nav-label">Compliance</div>
            <a href="#" class="nav-link">Weekly Reports</a>

            <div class="nav-label">Account</div>
            <a href="#" class="nav-link">Account Settings</a>
            <a href="#" class="nav-link logout">Sign Out</a>
        </nav>
    </aside>

    <div class="main-wrapper">
        <header>
            <input type="text" class="search-bar" placeholder="Search interns or logs...">
            <div class="header-actions">
                <span style="font-size: 0.85rem; color: var(--text-muted);">March 13, 2026</span>
            </div>
        </header>

        <main>
            <div class="section-header">
                <h2 style="margin: 0;">Intern Dashboard</h2>
            </div>

            <div class="admin-grid">
                <div class="metric-card">
                    <h4>Active Interns</h4>
                    <p>12</p>
                </div>
                <div class="metric-card">
                    <h4>Clocked In Today</h4>
                    <p style="color: var(--success);">10</p>
                </div>
                <div class="metric-card">
                    <h4>Pending Approvals</h4>
                    <p style="color: #f39c12;">24</p>
                </div>
                <div class="metric-card">
                    <h4>Avg. Compliance</h4>
                    <p>94%</p>
                </div>
            </div>

            <div class="data-panel">
                <div class="panel-header">
                    <h3 style="margin: 0;">Intern Status & Progress</h3>
                    <div style="color: var(--accent); font-weight: 600; cursor: pointer; font-size: 0.9rem; display: flex; align-items: center; gap: 5px;">
                        <i data-lucide="download" style="width: 16px;"></i> Export Report (CSV)
                    </div>
                </div>

                <div class="panel-filters">
                    <div class="filter-group">
                        <span class="filter-label">Work Location</span>
                        <select class="select-custom">
                            <option value="all">All Locations</option>
                            <option value="1nito">1Nito Tower</option>
                            <option value="avenir">Avenir Office</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <span class="filter-label">Completion Status</span>
                        <select class="select-custom">
                            <option value="all">All Status</option>
                            <option value="new">Just Started (< 25%)</option>
                            <option value="mid">Mid-way (50%)</option>
                            <option value="near">Near Completion (> 90%)</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <span class="filter-label">Schools</span>
                        <select class="select-custom">
                            <option value="all">All Schools</option>
                            <option value="new">Ateneo de Manila</option>
                            <option value="mid">Cebu Technological University</option>
                            <option value="near">University of Cebu</option>
                        </select>
                    </div>

                    <div class="filter-group" style="margin-left: auto; justify-content: flex-end;">
                        <button class="btn-action" style="height: 38px; background: #f8fafc;">
                            <i data-lucide="filter-x"></i> Clear
                        </button>
                    </div>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>Intern Name</th>
                            <th>Station/Office</th>
                            <th>Hours Rendered</th>
                            <th>Completion</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="user-cell">
                                    <div class="user-avatar-mini">
                                        <i data-lucide="user" style="width: 18px; color: var(--text-muted);"></i>
                                    </div>
                                    <div>
                                        <strong>Juan Dela Cruz</strong><br>
                                        <small style="color: var(--text-muted);">juan.d@omegahms.com</small>
                                    </div>
                                </div>
                            </td>
                            <td>1Nito Tower</td>
                            <td>120 / 480 hrs</td>
                            <td>
                                <div class="progress-bar-bg">
                                    <div class="progress-fill" style="width: 25%;"></div>
                                </div>
                            </td>
                            <td>
                                <button class="btn-action">
                                    <i data-lucide="file-text"></i> Logs
                                </button>
                                <button class="btn-action">
                                    <i data-lucide="edit-3"></i> Edit
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="user-cell">
                                    <div class="user-avatar-mini">
                                        <i data-lucide="user" style="width: 18px; color: var(--text-muted);"></i>
                                    </div>
                                    <div>
                                        <strong>Ferdinan Duterte</strong><br>
                                        <small style="color: var(--text-muted);">juan.d@omegahms.com</small>
                                    </div>
                                </div>
                            </td>
                            <td>UB Plaza</td>
                            <td>200 / 480 hrs</td>
                            <td>
                                <div class="progress-bar-bg">
                                    <div class="progress-fill" style="width: 42%;"></div>
                                </div>
                            </td>
                            <td>
                                <button class="btn-action">
                                    <i data-lucide="file-text"></i> Logs
                                </button>
                                <button class="btn-action">
                                    <i data-lucide="edit-3"></i> Edit
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <script>
        // Initialize Lucide Icons
        lucide.createIcons();
    </script>
</body>

</html>