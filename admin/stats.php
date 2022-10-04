<?php

    define('PAGE_TITLE', 'Statistics');

    require_once "../includes/config.php";
    require_once "../includes/db.php";
    require_once "../includes/helpers.php";
    require_once "../includes/queries.php";
    require_once "../includes/logging.php";

    session_start();
    // Client must be authenticated, otherwise redirect to homepage
    if(!isset($_SESSION["authenticated"])) { 
        logme(["Unauthenticated access to statistics.php."]);
        header("Location: /");
    }

    $user_id = $_SESSION["id"]; // Get the user's ID
    
    // Client must be an administrator, otherwise redirect to homepage
    if(!isset($_SESSION["is_admin"])) {
        logme(["userid", $user_id, "Non-administrative credential access to statistics.php."]);
        header("Location: /");    
    }

    logme(["userid", $user_id, "Visited statistics.php."]);
?>
<!doctype html>
<html lang='en-US'>
    <head>
        <?php include("../includes/head.php") ?>
    </head>
    <body>

    <?php include("../includes/header.html") ?>
    <?php include("../includes/topbar.php") ?>
    
    <h2>Statistics</h2>

    <ul>
        <li>Total Challenges: <?php echo(get_num_challenges()); ?></li>
        <li>Total Solves: <?php echo(get_num_solves()); ?></li>
        <li>Registered Users: <?php echo(get_num_accounts()); ?></li>
    </ul>

    <?php include("../includes/footer.php") ?>

    </body>
</html>