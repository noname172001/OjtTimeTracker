<?php
session_start();

$_SESSION['logged_user'] = "";

if (isset($_SESSION['logged_user'])) {
    include 'views/login_page.php';
}