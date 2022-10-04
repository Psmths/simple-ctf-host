<?php

    define('PAGE_TITLE', 'Leaderboard');

    require_once "../includes/config.php";
    require_once "../includes/db.php";
    require_once "../includes/queries.php";

    session_start();

    // Client must be authenticated, otherwise redirect to homepage
    if(!isset($_SESSION["authenticated"])) {
        header("Location: /");    
    }

    $leaderboard_array = get_leaderboard_array();

    function emoji_score($position) {
        if ($position == 1) {
            return "🥇";
        }
        if ($position == 2) {
            return "🥈";
        }
        if ($position == 3) {
            return "🥉";
        }
        return $position;
    }
?>
<!doctype html>
<html lang='en-US'>
    <head>
        <?php include("../includes/head.php") ?>
    </head>
    <body>

    <?php include("../includes/header.html") ?>
    <?php include("../includes/topbar.php") ?>

    <h2>Leaderboard</h2>

    <table>
        <?php
            $html = "<table><tr><th>Position</th><th>Username</th><th>Score</th></tr>";
            foreach($leaderboard_array as $key => $row) {
                $html .= "<tr>";
                    $html .= "<td class='position'>" . emoji_score($key+1) . "</td>";
                    $html .= "<td class='username'>" . user_id_to_name($row["user_id"]) . "</td>";
                    $html .= "<td class='score'>" . $row["score"] . "</td>";
                $html .= "</tr>";
            }
            echo($html);
        ?>
    </table>

    <?php include("../includes/footer.php") ?>

    </body>
</html>