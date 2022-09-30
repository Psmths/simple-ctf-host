<?php
    session_start();
?>
<!doctype html>
<html lang='en-US'>
    <head>
        <title>Simple CTF Framework</title>
        <?php include("./includes/html-head.html") ?>
    </head>
    <body>

    <?php include("./includes/header.html") ?>
    <?php include("./includes/topbar.php") ?>
    <?php include("./includes/ctf-description.html") ?>

    <?php 
        if(!isset($_SESSION["authenticated"])) {
            echo("<hr><center>You must first <a href=\"/login\">login</a> to access this CTF.</center>");    
        } else {
            // Show challenge table here
        }
    ?>

    <?php include("./includes/footer.php") ?>

    </body>
</html>