<?php

    require_once "../includes/config.php";
    require_once "../includes/db.php";
    require_once "../includes/helpers.php";
    require_once "../includes/logging.php";
    require_once "../includes/Parsedown.php";

    session_start();

    // Client must be authenticated, otherwise redirect to homepage
    if(!isset($_SESSION["authenticated"])) {
        header("Location: /");
        return; 
    }

    // Client must supply a challenge ID, otherwise redirect to homepage
    if (!isset($_GET['id'])) { 
        header("Location: /");
        return;
    }

    // Sanitize the client challenge ID
    $client_challenge_id = filter_input(INPUT_GET,"id",FILTER_SANITIZE_STRING);

    // Check if the challenge exists, otherwise redirect to homepage
    $sql = 'SELECT * FROM challenges WHERE id=:challenge_id';
    $statement = db()->prepare($sql);
    $statement->bindValue(':challenge_id', $client_challenge_id, PDO::PARAM_INT);
    $statement->execute();
    if ($statement->rowCount() == 0) {
        logme([
            "challenge", $client_challenge_id,
            "Attempt to access non-existent challenge ID."
        ]);
        header("Location: /");
    }

    // Connect to database and get challenge details for display
    $sql = 'SELECT name, text, flag FROM challenges WHERE id=:challenge_id';
    $statement = db()->prepare($sql);
    $statement->bindValue(':challenge_id', $client_challenge_id, PDO::PARAM_INT);
    $statement->execute();
    $result = $statement->fetch();

    $challenge_name = $result["name"];
    
    $challenge_flag = $result["flag"];

    $Parsedown = new Parsedown();
    $challenge_text = $Parsedown->text($result["text"]);

    $user_id = $_SESSION["id"]; // Get the user's ID

    define('PAGE_TITLE', $challenge_name);

    logme([
        "userid", $user_id,
        "challenge", $client_challenge_id, 
        "Challenge visited."
    ]);

    // Handler for the client when they submit a flag for validation
    if (isset($_POST['flag'])) { 
        
        $client_flag = trim(filter_input(INPUT_POST,"flag",FILTER_SANITIZE_STRING));
        if (strcmp($client_flag, $challenge_flag) !== 0) {
            $_SESSION['return_msg'] = "That's not the right flag!";
            logme([
                "userid", $user_id,
                "challenge", $client_challenge_id, 
                "Incorrect flag submitted.",
                "flag", $client_flag
            ]);
        } else {
            // Check if the user already solved this challenge
            $sql = 'SELECT * FROM solves WHERE challenge_id=:challenge_id AND user_id=:user_id';
            $statement = db()->prepare($sql);
            $statement->bindValue(':challenge_id', $client_challenge_id, PDO::PARAM_INT);
            $statement->bindValue(':user_id', $user_id, PDO::PARAM_INT);
            $statement->execute();
            if ($statement->rowCount() != 0) {
                $_SESSION['return_msg'] = "Flag correct! This challenge has already been solved.";
                logme([
                    "userid", $user_id,
                    "challenge", $client_challenge_id, 
                    "Flag submitted, but the user already solved.",
                    "flag", $client_flag
                ]);
            } else {
                // Add an entry to the solves database
                $sql = 'INSERT INTO solves(challenge_id, user_id) VALUES(:challenge_id, :user_id)';
                $statement = db()->prepare($sql);
                $statement->bindValue(':challenge_id', $client_challenge_id, PDO::PARAM_INT);
                $statement->bindValue(':user_id', $user_id, PDO::PARAM_INT);
                $statement->execute();

                $_SESSION['return_msg'] = "Flag correct!";
                logme([
                    "userid", $user_id,
                    "challenge", $client_challenge_id, 
                    "Correct flag submitted.",
                    "flag", $client_flag
                ]);
            }
        }
    }
?>
<!doctype html>
<html lang='en-US'>
    <head>
        <title>Simple CTF Framework</title>
        <?php include("../includes/head.php") ?>
    </head>
    <body>

    <?php include("../includes/header.html") ?>
    <?php include("../includes/topbar.php") ?>

    <h2><?php echo($challenge_name); ?></h2>
    <p><?php echo($challenge_text); ?></p>
    
    <center>
        <form action="/challenge/?id=<?php echo($client_challenge_id); ?>" method="post" autocomplete="off" style='display: inline'>
            <input type="text" name="flag" placeholder="Enter your flag here!" id="flag"><br><br>
            <input type="submit" value="Submit" style='display: inline'><br><br>
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