<?php

    define('PAGE_TITLE', 'Login');

    require_once "../includes/config.php";
    require_once "../includes/db.php";
    require_once "../includes/helpers.php";
    require_once "../includes/config.php";
    require_once "../includes/logging.php";

    if (isset($_POST['username'], $_POST['password'])) { 
        register();
    }

    function register() {

        // Sanitize client input
        $client_username = trim(filter_var($_POST['username'], FILTER_SANITIZE_STRING));
        $client_password = trim(filter_var($_POST['password'], FILTER_SANITIZE_STRING));

        // Check if registration form was completed
        if (empty($client_username) || empty($client_password)) {
            $_SESSION['return_msg'] = LOGIN_FAILED_ERROR;
            return;
        }

        // Check if user exists
        $sql = 'SELECT * FROM accounts WHERE username=:username';
        $statement = db()->prepare($sql);
        $statement->bindValue(':username', $client_username, PDO::PARAM_STR);
        $statement->execute();
        if ($statement->rowCount() == 0) {
            $_SESSION['return_msg'] = LOGIN_FAILED_ERROR;
            return;
        }
        
        // Connect to database and get credentials
        $sql = 'SELECT password_hash, id, last_logon, is_admin FROM accounts WHERE username = :username';
        $statement = db()->prepare($sql);
        $statement->bindValue(':username', $client_username, PDO::PARAM_STR);
        $statement->execute();
        $result = $statement->fetch();

        $db_password_hash = $result["password_hash"];
        $db_user_id = $result["id"];
        $db_last_logon =  $result["last_logon"];
        $is_admin =  $result["is_admin"];

        $password_correct = password_verify($client_password, $db_password_hash);

        if (!$password_correct) {
            $_SESSION['return_msg'] = LOGIN_FAILED_ERROR;
            return;
        }

        // Get timestamp and update last logon
        $timestamp = date('Y-m-d H:i:s');

        $sql = 'UPDATE accounts SET last_logon=TIMESTAMP(:last_logon) WHERE id=:id';
        $statement = db()->prepare($sql);
        $statement->bindValue(':id', $db_user_id, PDO::PARAM_INT);
        $statement->bindValue(':last_logon', $timestamp, PDO::PARAM_STR);
        $statement->execute();

        // Start the session and redirect to homepage
        session_start();
        $_SESSION['authenticated'] = true;
        $_SESSION['id'] = $db_user_id;
        $_SESSION['username'] = $client_username;
        $_SESSION['last_logon'] = $db_last_logon;

        $user_id = $_SESSION["id"]; // Get the user's ID
        logme(["userid", $user_id, "Logged on."]);

        // Set SESSION admin flag if the user is an admin
        if ($is_admin == 1) {
            $_SESSION["is_admin"] = true;
            logme(["userid", $user_id, "Authenticated as an administrator."]);
        }

        header("Location: /");
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
    
    <h2>Login to Simple CTF Framework</h2>

    <center>
        <form action="/login" method="post" autocomplete="off" style='display: inline'>
            <input type="text" name="username" placeholder="Username" id="username" required><br><br>
            <input type="password" name="password" placeholder="Password" id="password" required><br><br>
            <input type="submit" value="Register" style='display: inline'><br><br>
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