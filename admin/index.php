<?php

    define('PAGE_TITLE', 'Admin Panel');

    require_once "../includes/db.php";
    require_once "../includes/config.php";
    require_once "../includes/queries.php";
    require_once "../includes/logging.php";

    session_start();

    // Client must be authenticated, otherwise redirect to homepage
    if(!isset($_SESSION["authenticated"])) { 
        logme(["Unauthenticated access to admin panel."]);
        header("Location: /");
    }

    $user_id = $_SESSION["id"]; // Get the user's ID
    
    // Client must be an administrator, otherwise redirect to homepage
    if(!isset($_SESSION["is_admin"])) {
        logme(["userid", $user_id, "Non-administrative credential access to deletechallenge.php."]);
        header("Location: /");    
    }

    logme(["userid", $user_id, "Visited admin panel."]);
?>
<!doctype html>
<html lang='en-US'>
    <head>
        <?php include("../includes/head.php") ?>
    </head>
    <body>

    <?php include("../includes/header.html") ?>
    <?php include("../includes/topbar.php") ?>

    <h2>Administration Panel</h2>

    <h3>Administrative Activities</h3><br>
    <h4>Challenges</h4>
    <ul>
        <li><a href="/admin/newchallenge.php">Create a new challenge</a></li>
        <li><a href="/admin/modifychallenge.php">Modify challenges</a></li>
        <li><a href="/admin/deletechallenge.php">Delete challenges</a></li>
    </ul>

    <h4>Users</h4>
    <ul>
        <li><a href="/admin/modifyuser.php">Modify User</a></li>
    </ul>

    <h4>Miscellaneous</h4>
    <ul>
        <li><a href="/admin/stats.php">View Statistics</a></li>
        <li><a href="/admin/viewlog.php">View Log</a></li>
    </ul>

    

    <?php include("../includes/footer.php") ?>

    </body>
</html>