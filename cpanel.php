<?php
include 'includes/sessions.php';

$_SESSION['uid'] =  "userLoggedin";

var_dump($_SESSION);
echo '<br>';
var_dump(session_name());
echo '<br>';
var_dump($_COOKIE);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <a href="view/logout.php">Logout</a>
</body>
</html>