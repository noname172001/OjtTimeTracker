<?php

require_once '../includes/dbconfig.php';

/* Create the database */

echo "Preparing to connect to MySQL Server <br>";

try {

    $dsn = "mysql:host=" . DB_HOST;

    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    echo "Database connection established.<br>";
} catch (PDOException $e) {
    throw new PDOException("Error connection to the database server.<br>" . $e->getMessage());
}

echo "Creating the database<br>";


$query = "CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;";
//execute the query
try {
    $pdo->exec($query);
    echo "Database created <br>";
} catch (PDOException $e) {
    throw new PDOException("Error creating the database.<br>" . $e->getMessage());
}

/* Connect to the database */
try {
    $query = "USE `" . DB_NAME . "`";
    $pdo->exec($query);
    echo "Successfully selected the database <br>";
} catch (PDOException $e) {
    throw new PDOException("Error selecting the database.<br>" . $e->getMessage());
}


/* Create the tables */
