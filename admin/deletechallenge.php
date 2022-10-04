<?php

    define('PAGE_TITLE', 'Delete Challenge');

    require_once "../includes/config.php";
    require_once "../includes/db.php";
    require_once "../includes/helpers.php";
    require_once "../includes/queries.php";
    require_once "../includes/logging.php";

    session_start();
    // Client must be authenticated, otherwise redirect to homepage
    if(!isset($_SESSION["authenticated"])) { 
        logme(["Unauthenticated access to deletechallenge.php."]);
        header("Location: /");
        return;
    }

    $user_id = $_SESSION["id"]; // Get the user's ID
    
    // Client must be an administrator, otherwise redirect to homepage
    if(!isset($_SESSION["is_admin"])) {
        logme(["userid", $user_id, "Non-administrative credential access to deletechallenge.php."]);
        header("Location: /");
        return;  
    }

    logme(["userid", $user_id, "Visited deletechallenge.php."]);

    if (is_post_request()) { 
        delete_challenge();
    }

    function delete_challenge() {
        $challenge_name = filter_input(INPUT_POST,"challenge",FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        // Connect to database and delete the supplied challenge by name
        $sql = 'DELETE FROM challenges WHERE name = :challenge_name';
        $statement = db()->prepare($sql);
        $statement->bindValue('challenge_name', $challenge_name, PDO::PARAM_STR);
        $statement->execute();

        $_SESSION['return_msg'] = "Challenge deleted!";

        $user_id = $_SESSION["id"]; // Get the user's ID
        logme(["userid", $user_id, "Deleted challenge named:", $challenge_name]);

        return;
    }

    $challenges = query_all_challenges();
?>
<!doctype html>
<html lang='en-US'>
    <head>
        <?php include("../includes/head.php") ?>
    </head>
    <body>

    <?php include("../includes/header.html") ?>
    <?php include("../includes/topbar.php") ?>
    
    <h2>Delete Challenges</h2>

    <center>
        <span>Please select a challenge to delete:</span><br><br>
        <form action="/admin/deletechallenge.php" method="post">
            <select name="challenge" id="challenge">
            <?php
                foreach($challenges as $challenge){
                    
                    $challenge_name = $challenge["name"];
                    echo("<option value=\"".$challenge_name."\">".$challenge_name."</option>");
                }
            ?>
            </select>
            <br>
            <br>
            <input type="submit" value="Delete Challenge"><br><br>
        </form>
    </center>

    <?php
        if(!empty($_SESSION['return_msg'])){
            create_modal($_SESSION['return_msg']);
            unset($_SESSION['return_msg']);
        }
    ?>

    <?php include("../includes/footer.php") ?>

    </body>
</html>