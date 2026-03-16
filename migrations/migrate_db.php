<?php

require_once '../includes/dbconfig.php';

/* Style Definitions */
$green = "color: #28a745; font-weight: bold;";
$red   = "color: #dc3545; font-weight: bold;";
$blue  = "color: #007bff;";

/* START PRE */
echo "<pre><center>";
echo "<h2 style='$blue'>System Installation Log</h2>";
echo "<hr style='width:50%; opacity:0.3;'>";

/* 1. Database Connection */
echo "Preparing to connect to MySQL Server... ";
try {
    $dsn = "mysql:host=" . DB_HOST;
    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<span style='$green'>CONNECTED</span><br>";
} catch (PDOException $e) {
    echo "<span style='$red'>FAILED</span><br>";
    die("<p style='$red'>CRITICAL ERROR: " . $e->getMessage() . "</p>");
}

/* 2. Create Database */
echo "Creating database `" . DB_NAME . "`... ";
try {
    $query = "CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;";
    $pdo->exec($query);
    echo "<span style='$green'>SUCCESS</span><br>";
} catch (PDOException $e) {
    echo "<span style='$red'>FAILED</span><br>";
    die("<p style='$red'>ERROR: " . $e->getMessage() . "</p>");
}

/* 3. Select Database */
echo "Selecting database... ";
try {
    $pdo->exec("USE `" . DB_NAME . "`");
    echo "<span style='$green'>SELECTED</span><br>";
} catch (PDOException $e) {
    echo "<span style='$red'>FAILED</span><br>";
    die("<p style='$red'>ERROR: " . $e->getMessage() . "</p>");
}

echo "<hr style='width:30%; opacity:0.2;'>";

/* 4. Create Tables */
$tables = [
    "users" => "CREATE TABLE IF NOT EXISTS users (
            users_id INT AUTO_INCREMENT PRIMARY KEY,
            first_name VARCHAR(50) NOT NULL,
            middle_name VARCHAR(50),
            last_name VARCHAR(50) NOT NULL,
            school VARCHAR(100),
            total_required_hours INT DEFAULT 0,
            site_location VARCHAR(50),
            user_address TEXT,
            mobile_no VARCHAR(30),
            status ENUM('active','inactive','suspended','deleted') DEFAULT 'active',
            date_status_updated DATETIME DEFAULT NULL,
            date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    "users_login" => "CREATE TABLE IF NOT EXISTS users_login (
            id INT AUTO_INCREMENT PRIMARY KEY,
            users_id INT NOT NULL,
            email VARCHAR(150) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            role ENUM('admin','intern') NOT NULL,
            CONSTRAINT fk_user_login
                FOREIGN KEY (users_id)
                REFERENCES users(users_id)
                ON DELETE CASCADE
                ON UPDATE CASCADE
            )ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    "timesheet" => "CREATE TABLE IF NOT EXISTS timesheet (
            timesheet_id INT AUTO_INCREMENT PRIMARY KEY,
            users_id INT NOT NULL,
            log_date DATE NOT NULL,
            log_time_in TIME NOT NULL,
            log_time_out TIME DEFAULT NULL,
            total_log_hours DECIMAL(5,2) DEFAULT 0.00,
            create_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            update_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            CONSTRAINT fk_timesheet_user
                FOREIGN KEY (users_id)
                REFERENCES users(users_id)
                ON DELETE CASCADE
                ON UPDATE CASCADE
            )ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
];

foreach ($tables as $tableName => $sql) {
    echo "Creating table `$tableName`... ";
    try {
        $pdo->exec($sql);
        echo "<span style='$green'>OK</span><br>";
    } catch (PDOException $e) {
        echo "<span style='$red'>ERROR</span><br>";
        echo "<small style='$red'>" . $e->getMessage() . "</small><br>";
    }
}

echo "<hr style='width:50%; opacity:0.3;'>";
echo "<h3 style='$green'>Migration Complete</h3>";

/* END PRE */
echo "</center></pre>";
