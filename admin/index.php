<?php
    session_start();

    // Client must be authenticated, otherwise redirect to homepage
    if(!isset($_SESSION["authenticated"])) {
        header("Location: /");    
    }

    // Client must be an administrator, otherwise redirect to homepage
    if(!isset($_SESSION["is_admin"])) {
        header("Location: /");    
    }

    require_once "../includes/db.php";
    require_once "../includes/config.php";
    require_once "../includes/queries.php";
?>
<!doctype html>
<html lang='en-US'>
    <head>
        <title>Simple CTF Framework</title>
        <?php include("../includes/html-head.html") ?>
    </head>
    <body>

    <?php include("../includes/header.html") ?>
    <?php include("../includes/topbar.php") ?>

    <h2>Administration Panel</h2>

    <h3>Administrative Activities</h3>
    <ul>
        <li><a href="/admin/newchallenge.php">Create a new challenge</a></li>
    </ul>

    

    <?php include("../includes/footer.php") ?>

    </body>
</html>