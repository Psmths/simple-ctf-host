<?php

    require_once "../includes/config.php";
    require_once "../includes/db.php";

    session_start();

    // Client must be authenticated, otherwise redirect to homepage
    if(!isset($_SESSION["authenticated"])) {
        header("Location: /");    
    }

    // Client must supply a challenge ID, otherwise redirect to homepage
    if (!isset($_GET['id'])) { 
        header("Location: /");
    }

    // Sanitize the client challenge ID
    $client_challenge_id = filter_input(INPUT_GET,"id",FILTER_SANITIZE_STRING);

    // Check if the challenge exists, otherwise redirect to homepage
    $sql = 'SELECT * FROM challenges WHERE id=:challenge_id';
    $statement = db()->prepare($sql);
    $statement->bindValue(':challenge_id', $client_challenge_id, PDO::PARAM_INT);
    $statement->execute();
    if ($statement->rowCount() == 0) {
        header("Location: /");
    }

    // Connect to database and get challenge details for display
    $sql = 'SELECT name, text, flag FROM challenges WHERE id=:challenge_id';
    $statement = db()->prepare($sql);
    $statement->bindValue(':challenge_id', $client_challenge_id, PDO::PARAM_INT);
    $statement->execute();
    $result = $statement->fetch();

    $challenge_name = $result["name"];
    $challenge_text = $result["text"];
    $challenge_flag = $result["flag"];

    // Handler for the client when they submit a flag for validation
    if (isset($_POST['flag'])) { 
    
        $client_flag = trim(filter_input(INPUT_POST,"flag",FILTER_SANITIZE_STRING));
        if (strcmp($client_flag, $challenge_flag) !== 0) {
            $_SESSION['return_msg'] = "That's not the right flag!";
        } else {
            $user_id = $_SESSION["id"]; // Get the user's ID

            // Check if the user already solved this challenge
            $sql = 'SELECT * FROM solves WHERE challenge_id=:challenge_id AND user_id=:user_id';
            $statement = db()->prepare($sql);
            $statement->bindValue(':challenge_id', $client_challenge_id, PDO::PARAM_INT);
            $statement->bindValue(':user_id', $user_id, PDO::PARAM_INT);
            $statement->execute();
            if ($statement->rowCount() != 0) {
                $_SESSION['return_msg'] = "Flag correct! This challenge has already been solved.";
            } else {
                // Add an entry to the solves database
                $sql = 'INSERT INTO solves(challenge_id, user_id) VALUES(:challenge_id, :user_id)';
                $statement = db()->prepare($sql);
                $statement->bindValue(':challenge_id', $client_challenge_id, PDO::PARAM_INT);
                $statement->bindValue(':user_id', $user_id, PDO::PARAM_INT);
                $statement->execute();

                $_SESSION['return_msg'] = "Flag correct!";
            }
        }
    }
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
            echo("<center><span>".$_SESSION['return_msg']."</span></center>");
            unset($_SESSION['return_msg']);
        }
    ?>

    <?php include("../includes/footer.php") ?>

    </body>
</html>