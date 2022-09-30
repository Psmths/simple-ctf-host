<?php
    session_start();
    $_SESSION = array();
    session_destroy();
?>
<!doctype html>
<html lang='en-US'>
    <head>
        <title>Simple CTF Framework - Logged Out</title>
        <?php include("../includes/html-head.html") ?>
    </head>
    <body>

    <?php include("../includes/header.html") ?>
    <?php include("../includes/topbar.php") ?>
    
    <h2>Logged Out</h2>

    <p>You have successfully logged out!</p>

    <?php include("../includes/footer.php") ?>

    </body>
</html>