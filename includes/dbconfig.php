<?php

return [
    'db' => [
        'host' => 'localhost',
        'name' => 'timetrackerdb',
        'user' => 'root',
        'password' => '',
        'charset' => 'utf8mb4',
    ],
    'pdo_options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,

    ],
];