<?php

define('PAGE_TITLE', '');

    session_start();
    require_once "./includes/db.php";
    require_once "./includes/config.php";
    require_once "./includes/queries.php";
    require_once "./includes/helpers.php";
?>
<!doctype html>
<html lang='en-US'>
    <head>
        <?php include("./includes/head.php") ?>
    </head>
    <body>

    <?php include("./includes/header.html") ?>
    <?php include("./includes/topbar.php") ?>
    <?php include("./includes/ctf-description.html") ?>

    <?php 
        if(!isset($_SESSION["authenticated"])) {
            echo("<hr><center>You must first <a href=\"/login\">login</a> to access this CTF.</center>");    
        } else {
            // Display the challenge deck
            $challenge_array = get_challenge_array();
            
            
            foreach($challenge_array as $category) {
                
                echo("<h2>".$category["category"]."</h2>");
                echo("<table>");
                echo("
                <table>
                    <tr>
                        <th>Status</th>
                        <th>Challenge</th>
                        <th>Topic</th>
                        <th>Difficulty</th>
                        <th>Points</th>
                        <th>Solves</th>
                    </tr>
                ");

                foreach($category["challenges"] as $challenge) {
                    echo("<tr>");
                    $challenge_id = $challenge["id"];
                    $solved = query_user_solve_status($_SESSION["id"], $challenge_id);
                    if ($solved) {
                        echo("<td class=\"status\">✅</td>");
                    } else {
                        echo("<td class=\"status\"></td>");
                    }

                    echo("<td class=\"name\"><a href=\"/challenge/?id=".$challenge_id."\">".$challenge["name"]."</a></td>");
                    echo("<td class=\"topic\">".$challenge["subcategory"]."</td>");
                    echo("<td class=\"difficulty\">".format_difficulty($challenge["difficulty"])."</td>");

                    $challenge_points_value = query_challenge_points($challenge_id);
                    echo("<td class=\"points\">".$challenge_points_value."</td>");

                    $challenge_solves = query_challenge_solves($challenge_id);
                    echo("<td class=\"solves\">".$challenge_solves."</td>");

                    echo("</tr>");
                    }
                echo("</table>");
                }
                
            
        }
    ?>

    <?php include("./includes/footer.php") ?>

    </body>
</html>