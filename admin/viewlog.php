<?php

    define('PAGE_TITLE', 'View Log');

    require_once "../includes/config.php";
    require_once "../includes/db.php";
    require_once "../includes/helpers.php";
    require_once "../includes/queries.php";
    require_once "../includes/logging.php";

    session_start();
    // Client must be authenticated, otherwise redirect to homepage
    if(!isset($_SESSION["authenticated"])) { 
        logme(["Unauthenticated access to viewlog.php."]);
        header("Location: /");
    }

    $user_id = $_SESSION["id"]; // Get the user's ID
    
    // Client must be an administrator, otherwise redirect to homepage
    if(!isset($_SESSION["is_admin"])) {
        logme(["userid", $user_id, "Non-administrative credential access to viewlog.php."]);
        header("Location: /");    
    }

    logme(["userid", $user_id, "Visited viewlog.php."]);
?>
<!doctype html>
<html lang='en-US'>
    <head>
        <?php include("../includes/head.php") ?>
    </head>
    <body>

    <?php include("../includes/header.html") ?>
    <?php include("../includes/topbar.php") ?>
    
    <h2>Recent Log Entries</h2>

    <p>Sorted in descending order by time, last 100 log lines.</p>

    <pre><?php
        $LINES_TO_DIPLAY = 100;

        $logfile = LOG_DIRECTORY.LOG_NAME.".log";
        $linecount = 0;
        $handleFile = fopen($logfile, "r");
        
        while(!feof($handleFile)){
            $line = fgets($handleFile);
            $linecount++;
        }

        if ($LINES_TO_DIPLAY > $linecount) {
            $startline = 0;
        } else {
            $startline = $linecount - $LINES_TO_DIPLAY;
        }

        $spl_file = new SplFileObject($logfile);

        for ($i = $linecount; $i > $startline; $i--) {
            $spl_file->seek($i);
            echo($spl_file->current());
        }
    
        fclose($handleFile);

        
    ?></pre>

    <?php include("../includes/footer.php") ?>

    </body>
</html>