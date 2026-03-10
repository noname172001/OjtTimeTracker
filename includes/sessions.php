<?php
session_start();


function logout() 
{
    // unset all the session vars
    $_SESSION = array();

    // destroy session cookies if any
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
        );
    }

    session_unset();
    session_destroy();

    // redirect to login/landing page
    header("Location: ../index.php");
    exit;
}