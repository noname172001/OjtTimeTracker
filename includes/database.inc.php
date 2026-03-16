<?php

$config = require 'dbconfig.php';

// create the dsn
$dsn = "mysql:host={$config['db']['host']};dbname={$config['db']['name']};charset={$config['db']['charset']}";

try {
    $pdo = new PDO($dsn, $config['db']['user'], $config['db']['password'], $config['db']['pdo_options']);
    return $pdo;
} catch (PDOException $e) {
    throw new PDOException("Error: Database connection.".$e->getMessage());
}