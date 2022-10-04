<?php

define('PAGE_TITLE', 'Logout');

    require_once "../includes/helpers.php";
    require_once "../includes/config.php";
    require_once "../includes/logging.php";

    session_start();
    $user_id = $_SESSION["id"]; // Get the user's ID
    $_SESSION = array();
    session_destroy();
    logme(["userid", $user_id, "Logged off."]);
?>
<!doctype html>
<html lang='en-US'>
    <head>
        <?php include("../includes/head.php") ?>
    </head>
    <body>

    <?php include("../includes/header.html") ?>
    <?php include("../includes/topbar.php") ?>
    
    <h2>Logged Out</h2>

    <p>You have successfully logged out!</p>

    <?php include("../includes/footer.php") ?>

    </body>
</html>