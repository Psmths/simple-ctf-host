<?php

define('PAGE_TITLE', 'New Challenge');

    require_once "../includes/config.php";
    require_once "../includes/db.php";
    require_once "../includes/helpers.php";
    require_once "../includes/queries.php";
    require_once "../includes/logging.php";

    session_start();
    // Client must be authenticated, otherwise redirect to homepage
    if(!isset($_SESSION["authenticated"])) { 
        logme(["Unauthenticated access to newchallenge.php."]);
        header("Location: /");
    }

    $user_id = $_SESSION["id"]; // Get the user's ID

    // Client must be an administrator, otherwise redirect to homepage
    if(!isset($_SESSION["is_admin"])) {
        logme(["userid", $user_id, "Non-administrative credential access to newchallenge.php."]);
        header("Location: /");    
    }

    logme(["userid", $user_id, "Visited newchallenge.php."]);

    if (is_post_request()) { 
        create_new_challenge();
    } else {
        // These session variables save form input for convenience.
        unset_returns();
    }

    function unset_returns() {
        unset($_SESSION['form_name']);
        unset($_SESSION['form_text']);
        unset($_SESSION['form_category']);
        unset($_SESSION['form_subcategory']);
        unset($_SESSION['form_difficulty']);
        unset($_SESSION['form_flag']);
    }

    function create_new_challenge() {

        $user_id = $_SESSION["id"]; // Get the user's ID

        // Validate and sanitize the input

        $client_name = trim(filter_input(INPUT_POST,"name",FILTER_SANITIZE_STRING));
        $_SESSION['form_name'] = $client_name;

        $client_category = trim(filter_input(INPUT_POST,"category",FILTER_SANITIZE_STRING));
        $_SESSION['form_category'] = $client_category;

        $client_subcategory = trim(filter_input(INPUT_POST,"subcategory",FILTER_SANITIZE_STRING));
        $_SESSION['form_subcategory'] = $client_subcategory;

        $client_text = filter_input(INPUT_POST,"text",FILTER_SANITIZE_STRING);
        $_SESSION['form_text'] = $client_text;

        $validate_difficulty = filter_input(
            INPUT_POST,
            "difficulty",
            FILTER_VALIDATE_INT,
            array(
                'options' => array(
                    'min_range' => MIN_CHALLENGE_DIFFICULTY, 
                    'max_range' => MAX_CHALLENGE_DIFFICULTY
                )
        ));
        $client_difficulty = trim(filter_input(INPUT_POST,"difficulty",FILTER_SANITIZE_NUMBER_INT));
        $_SESSION['form_difficulty'] = $client_difficulty;

        $client_flag = trim(filter_input(INPUT_POST,"flag",FILTER_SANITIZE_FULL_SPECIAL_CHARS));
        $_SESSION['form_flag'] = $client_flag;


        // Return validation errors
        if (!$validate_difficulty) {
            $_SESSION['return_msg'] = "Please enter a valid challenge difficulty.";
            return;
        }

        // Connect to database and add the new challenge
        $sql = 'INSERT INTO challenges(name, text, category, subcategory, difficulty, flag) VALUES(:name, :text, :category, :subcategory, :difficulty, :flag)';
        $statement = db()->prepare($sql);
        $statement->bindValue(':name', $client_name, PDO::PARAM_STR);
        $statement->bindValue(':text', $client_text, PDO::PARAM_STR);
        $statement->bindValue(':category', $client_category, PDO::PARAM_STR);
        $statement->bindValue(':subcategory', $client_subcategory, PDO::PARAM_STR);
        $statement->bindValue(':difficulty', $client_difficulty, PDO::PARAM_INT);
        $statement->bindValue(':flag', $client_flag, PDO::PARAM_STR);
        $statement->execute();

        // Connect to database and get the new challenge's ID
        $new_challenge_id = challenge_name_to_id($client_name);
        // Add files to db if they were sent
        // Not sent would be indicated by error = int(4)
        if (!($_FILES["challenge_files"]["error"] == 4)) {
            // Transfer file to the specified storage directory
            $file_name = $_FILES["challenge_files"]["name"];
            $file_tmp_path = $_FILES["challenge_files"]["tmp_name"];
            $final_file_path = FILE_STORE_DIRECTORY.$file_name;
            rename($file_tmp_path, $final_file_path);
            // Connect to database and add the new file
            $sql = 'INSERT INTO challenge_files(challenge_id, location) VALUES(:challenge_id, :location)';
            $statement = db()->prepare($sql);
            $statement->bindValue(':challenge_id', $new_challenge_id, PDO::PARAM_INT);
            $statement->bindValue(':location', $final_file_path, PDO::PARAM_STR);
            $statement->execute();

            logme(["userid", $user_id, 
            "File uploaded for challenge:", $new_challenge_id,
            "name:", $file_name,
            "size:", $_FILES["challenge_files"]["size"]
        ]);
        }

        unset_returns();
        
        logme(["userid", $user_id, "New challenge created with ID:", $new_challenge_id]);
        $_SESSION['return_msg'] = "Challenge added! You may view it <a href=\"/challenge?id=".$new_challenge_id."\">here</a>.";
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
    
    <h2>Create a New Challenge</h2>

    <center>
        <form action="/admin/newchallenge.php" method="post" autocomplete="off" style='display: inline' enctype="multipart/form-data">
            <input type="text" name="name" placeholder="Challenge Name" <?php if (isset($_SESSION['form_name'])) echo("value=\"".$_SESSION['form_name']."\""); ?> id="name" required><br><br>

            <textarea type="text" name="text" placeholder="Challenge Text (Markdown)"id="text"><?php if (isset($_SESSION['form_text'])) echo($_SESSION['form_text']); ?></textarea><br><br>

            <input type="text" name="category" placeholder="Challenge Category" <?php if (isset($_SESSION['form_category'])) echo("value=\"".$_SESSION['form_category']."\""); ?> id="category" required><br><br>

            <input type="text" name="subcategory" placeholder="Challenge Topic" <?php if (isset($_SESSION['form_subcategory'])) echo("value=\"".$_SESSION['form_subcategory']."\""); ?> id="subcategory" required><br><br>

            <input type="text" name="difficulty" placeholder="Difficulty" <?php if (isset($_SESSION['form_difficulty'])) echo("value=\"".$_SESSION['form_difficulty']."\""); ?> id="difficulty" required><br><br>

            <input type="text" name="flag" placeholder="Challenge Flag" <?php if (isset($_SESSION['form_flag'])) echo("value=\"".$_SESSION['form_flag']."\""); ?> id="flag" required><br><br>

            <center>
                <span>Challenge Files (Optional)</span><br><br>
            </center>
            <input type="file" name="challenge_files"/><br><br>

            <input type="submit" value="Create Challenge" style='display: inline'><br><br>
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